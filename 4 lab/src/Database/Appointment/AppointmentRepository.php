<?php
declare(strict_types=1);

namespace App\Database\Appointment;

use App\Database\Connection;
use App\Model\Appointment;
use App\Model\Repository\AppointmentRepositoryInterface;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findOne(int $id): ?Appointment
    {
        $query = <<< SQL
            SELECT appointment_id, client_id, master_id, date
            FROM appointment
            WHERE appointment_id = $id
        SQL;

        $statement = $this->connection->execute($query);
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->hydrateAppointment($row);
        }

        return null;
    }

    public function save(Appointment $appointment): int
    {
        $appointmentId = $appointment->getId();
        if ($appointmentId)
        {
            $this->updateAppointment($appointment);
        }
        else
        {
            $appointmentId = $this->insertAppointment($appointment);
            $appointment->assignIdentifier($appointmentId);
        }

        return $appointmentId;
    }

    public function delete(int $id): bool
    {
        $query = <<< SQL
            DELETE FROM appointment
            WHERE appointment_id = $id
        SQL;

        $this->connection->execute($query);

        if(!$this->findOne($id))
        {
            return true;
        }
        return false;
    }

    private function hydrateAppointment(array $row): Appointment
    {
        try {
            return new Appointment(
                id: (int)$row["appointment_id"],
                masterId: $row["master_id"],
                clientId: $row["client_id"],
                date: new \DateTimeImmutable($row["date"])
            );
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function insertAppointment(Appointment $appointment): int
    {
        $query = <<<SQL
            INSERT INTO appointment (client_id, master_id, date)
            VALUES (:client_id, :master_id, :date)
            SQL;
        $params = [
            ":client_id" => $appointment->getClientId(),
            ":master_id" => $appointment->getMasterId(),
            ":date" => $appointment->getDate()->format('Y-m-d H:i:s'),
        ];

        $this->connection->execute($query, $params);

        return $this->connection->getLastInsertId();
    }

    private function updateAppointment(Appointment $appointment): void
    {
        $query = <<<SQL
            UPDATE appointment 
            SET client_id = :client_id, master_id = :master_id, date = :date
            WHERE appointment_id = :id
            SQL;
        $params = [
            ":id" => $appointment->getId(),
            ":client_id" => $appointment->getClientId(),
            ":master_id" => $appointment->getMasterId(),
            ":date" => $appointment->getDate()>format('Y-m-d H:i:s'),
        ];

        $stmt = $this->connection->execute($query, $params);
        if (!$stmt->rowCount())
        {
            throw new \Exception("Optimistic lock failed for article {$appointment->getId()}");
        }
    }
}