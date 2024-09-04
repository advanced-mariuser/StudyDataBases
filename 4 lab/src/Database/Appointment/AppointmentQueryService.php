<?php
declare(strict_types=1);

namespace App\Database\Appointment;

use App\Database\Connection;
use App\Model\Data\Appointment\AppointmentSummary;

class AppointmentQueryService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return AppointmentSummary[]
     */
    public function listAppointments(?int $masterId, ?int $clientId): array
    {
        if ($masterId !== null) {
            $query = <<< SQL
            SELECT DATE(date) AS appointment_date, GROUP_CONCAT(appointment_id) AS appointment_ids
            FROM appointment
            WHERE master_id = $masterId
            GROUP BY DATE(date)
            ORDER BY master_id
            SQL;
        } else if ($clientId !== null) {
            $query = <<< SQL
            SELECT DATE(date) AS appointment_date, GROUP_CONCAT(appointment_id) AS appointment_ids
            FROM appointment
            WHERE client_id = $clientId
            GROUP BY DATE(date)
            ORDER BY client_id
            SQL;
        }

        $appointments = [];
        $stmt = $this->connection->execute($query);
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $appointmentIds = explode(',', $row['appointment_ids']);
            $appointments[] = new AppointmentSummary(
                $appointmentIds,
                new \DateTimeImmutable($row['appointment_date'])
            );
        }

        return $appointments;

    }
}