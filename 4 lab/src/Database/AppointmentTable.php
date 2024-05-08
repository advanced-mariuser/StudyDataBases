<?php
declare(strict_types=1);

namespace App\Database;

use App\Model\Appointment;

class AppointmentTable
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getAllAppointments(?int $masterId, ?int $clientId): array|null
    {
        if ($masterId !== null) {
            $query = <<< SQL
            SELECT DATE(date) AS appointment_date, GROUP_CONCAT(appointment_id) AS appointment_ids
            FROM appointment
            WHERE master_id = $masterId
            GROUP BY DATE(date)
            SQL;
        } else if ($clientId !== null) {
            $query = <<< SQL
            SELECT DATE(date) AS appointment_date, GROUP_CONCAT(appointment_id) AS appointment_ids
            FROM appointment
            WHERE client_id = $clientId
            GROUP BY DATE(date)
            SQL;
        }

        $appointments = [];

        $statement = $this->connection->query($query);
        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $appointmentIds = explode(',', $row['appointment_ids']);
            $appointments[$row['appointment_date']] = $appointmentIds;
        }

        if($appointments)
        {
            return $appointments;
        }
        return null;
    }

    public function findAppointment(int $id): ?Appointment
    {
        $query = <<< SQL
            SELECT *
            FROM appointment
            WHERE appointment_id = $id
        SQL;

        $statement = $this->connection->query($query);
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->createAppointmentFromRow($row);
        }

        return null;
    }

    function createAppointment(int $clientId, int $masterId, \DateTimeImmutable $date): int
    {
        $query = <<< SQL
            INSERT INTO appointment (client_id, master_id, date)
            VALUES (:client_id, :master_id, :date)
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([
            ":client_id" => $clientId,
            ":master_id" => $masterId,
            ":date" => $date->format('Y-m-d H:i:s'),
        ]);

        return (int)$this->connection->lastInsertId();
    }

    function editAppointment(int $appointmentId, int $clientId, int $masterId, \DateTimeImmutable $date): int
    {
        $query = <<< SQL
            UPDATE appointment 
            SET client_id = :client_id,master_id =  :master_id,date = :date
            WHERE appointment_id = :id
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([
            ":id" => $appointmentId,
            ":client_id" => $clientId,
            ":master_id" => $masterId,
            ":date" => $date->format('Y-m-d'),
        ]);

        return (int)$this->connection->lastInsertId();
    }

    function deleteAppointment(int $appointmentId): bool
    {
        $query = <<< SQL
            DELETE FROM appointment
            WHERE appointment_id = :id
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([
            ":id" => $appointmentId
        ]);

        return true;
    }

    private function createAppointmentFromRow(array $row): Appointment
    {
        return new Appointment(
            id: (int)$row["appointment_id"],
            masterId: (int)$row["master_id"],
            clientId: (int)$row["client_id"],
            date: new \DateTimeImmutable($row["date"]),
            createdAt: new \DateTimeImmutable($row["created_at"]),
            updatedAt: new \DateTimeImmutable($row["updated_at"]),
        );
    }

//    private function createAppointmentFromRow(array $row): Appointment
//    {
//        return new Appointment(
//            id: (int)$row["appointment_id"],
//            masterId: (int)$row["master_id"],
//            clientId: (int)$row["client_id"],
//            createdAt: new \DateTimeImmutable($row["created_at"]),
//            updatedAt: new \DateTimeImmutable($row["updated_at"]),
//        );
//    }
}