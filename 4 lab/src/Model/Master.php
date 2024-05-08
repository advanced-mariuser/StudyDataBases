<?php
declare(strict_types=1);

namespace App\Model;

readonly class Master
{
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName,
        private string $phone,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}