<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Appointment;
use App\Model\Master;

interface MasterRepositoryInterface
{
    public function findOne(int $id): ?Master;
    function save(Master $master): int;
    function delete(int $id): bool;
}