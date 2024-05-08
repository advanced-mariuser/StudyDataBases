<?php
declare(strict_types=1);

namespace App\Model;

readonly class Appointment
{
    public function __construct(
        private int $id,
        private ?int $masterId,
        private ?int $clientId,
        private \DateTimeImmutable $date,
        private ?\DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $updatedAt,
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMasterId(): ?int
    {
        return $this->masterId;
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
