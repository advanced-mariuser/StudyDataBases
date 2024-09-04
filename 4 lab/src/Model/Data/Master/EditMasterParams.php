<?php
declare(strict_types=1);

namespace App\Model\Data\Master;

use DateTimeImmutable;
class EditMasterParams
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
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->id = $id;
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