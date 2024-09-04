<?php
declare(strict_types=1);

namespace App\Model\Data\Client;

use DateTimeImmutable;
class EditClientParams
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $phone;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $phone,
    )
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
    }

    public function getId(): int
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
}