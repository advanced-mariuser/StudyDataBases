<?php
declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;
class Master
{
    private ?int $id;
    private string $firstName;
    private string $lastName;
    private string $phone;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        ?int $id,
        string $firstName,
        string $lastName,
        string $phone,
        ?DateTimeImmutable $createdAt = new DateTimeImmutable('now'),
        ?DateTimeImmutable $updatedAt = new DateTimeImmutable('now')
    )
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function assignIdentifier(int $id): void
    {
        $this->id = $id;
    }

    public function edit(string $firstName, string $lastName, string $phone): void
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;

        $this->updatedAt = new DateTimeImmutable();
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

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}