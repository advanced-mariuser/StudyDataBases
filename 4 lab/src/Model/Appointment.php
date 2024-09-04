<?php
declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

class Appointment
{
    private ?int $id;
    private int $masterId;
    private int $clientId;
    private DateTimeImmutable $date;

    public function __construct(
        ?int $id,
        int $masterId,
        int $clientId,
        DateTimeImmutable $date,
    )
    {
        $this->id = $id;
        $this->masterId = $masterId;
        $this->clientId = $clientId;
        $this->date = $date;
    }

    public function assignIdentifier(int $id): void
    {
        $this->id = $id;
    }

    public function edit(int $masterId, int $clientId, DateTimeImmutable $date): void
    {
        $this->masterId = $masterId;
        $this->clientId = $clientId;
        $this->date = $date;
    }

    public function getId(): ?int
    {
        return $this->id;
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