<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Appointment;

interface AppointmentRepositoryInterface
{
    public function findOne(int $id): ?Appointment;
    function save(Appointment $appointment): int;
    function delete(int $id): bool;
}