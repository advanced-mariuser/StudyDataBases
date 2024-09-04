<?php
declare(strict_types=1);

namespace App\Database\Master;

use App\Database\Connection;
use App\Model\Master;
use App\Model\Repository\MasterRepositoryInterface;

class MasterRepository implements MasterRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findOne(int $id): ?Master
    {
        $query = <<< SQL
            SELECT master_id, first_name, last_name, phone, created_at, updated_at
            FROM master
            WHERE master.master_id = $id
        SQL;

        $statement = $this->connection->execute($query);
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->hydrateMaster($row);
        }

        return null;
    }

    public function save(Master $master): int
    {
        $masterId = $master->getId();
        if ($masterId)
        {
            $this->updateMaster($master);
        }
        else
        {
            $masterId = $this->insertMaster($master);
            $master->assignIdentifier($masterId);
        }

        return $masterId;
    }

    public function delete(int $id): bool
    {
        $query = <<< SQL
            DELETE FROM master
            WHERE master_id = $id
        SQL;

        $this->connection->execute($query);

        if(!$this->findOne($id))
        {
            return true;
        }
        return false;
    }

    private function hydrateMaster(array $row): Master
    {
        try {
            return new Master(
                id: (int)$row["master_id"],
                firstName: $row["first_name"],
                lastName: $row["last_name"],
                phone: $row["phone"],
                createdAt: new \DateTimeImmutable($row["created_at"]),
                updatedAt: new \DateTimeImmutable($row["updated_at"]),
            );
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function insertMaster(Master $master): int
    {
        $query = <<<SQL
            INSERT INTO master (first_name, last_name, phone)
            VALUES (:first_name, :last_name, :phone)
            SQL;
        $params = [
            ":first_name" => $master->getFirstName(),
            ":last_name" => $master->getLastName(),
            ":phone" => $master->getPhone(),
        ];

        $this->connection->execute($query, $params);

        return $this->connection->getLastInsertId();
    }

    private function updateMaster(Master $master): void
    {
        $query = <<<SQL
            UPDATE master 
            SET first_name = :first_name,last_name =  :last_name,phone = :phone
            WHERE master_id = :id
            SQL;
        $params = [
            ":id" => $master->getId(),
            ":first_name" => $master->getFirstName(),
            ":last_name" => $master->getLastName(),
            ":phone" => $master->getPhone(),
        ];

        $stmt = $this->connection->execute($query, $params);
        if (!$stmt->rowCount())
        {
            throw new \Exception("Optimistic lock failed for article {$master->getId()}");
        }
    }
}