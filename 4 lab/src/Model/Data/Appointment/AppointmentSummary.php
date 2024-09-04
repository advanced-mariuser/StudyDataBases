<?php
declare(strict_types=1);

namespace App\Model\Data\Appointment;

use DateTimeImmutable;

class AppointmentSummary
{
    private DateTimeImmutable $date;
    private array $ids;
    public function __construct(
        array $ids,
        DateTimeImmutable $date,
    )
    {
        $this->date = $date;
        $this->ids = $ids;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getIds(): array
    {
        return $this->ids;
    }
}