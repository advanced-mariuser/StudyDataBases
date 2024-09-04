<?php
declare(strict_types=1);

namespace App\Model\Data\Appointment;

use DateTimeImmutable;
class CreateAppointmentParams
{
    private int $masterId;
    private int $clientId;
    private DateTimeImmutable $date;

    public function __construct(
        int $masterId,
        int $clientId,
        DateTimeImmutable $date,
    )
    {
        $this->masterId = $masterId;
        $this->clientId = $clientId;
        $this->date = $date;
    }

    public function getMasterId(): int
    {
        return $this->masterId;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}