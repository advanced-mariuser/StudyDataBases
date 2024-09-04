<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Appointment;
use App\Model\Client;

interface ClientRepositoryInterface
{
    public function findOne(int $id): ?Client;
    public function save(Client $client): int;
}