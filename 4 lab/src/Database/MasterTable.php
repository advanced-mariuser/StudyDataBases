<?php
declare(strict_types=1);

namespace App\Database;

use App\Model\Master;

class MasterTable
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Master[]
     */
    public function getAllMasters(): array
    {
        $query = <<< SQL
            SELECT *
            FROM master
        SQL;

        $statement = $this->connection->query($query);
        $masters = [];
        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $masters[] = $this->createMasterFromRow($row);
        }

        return $masters;
    }

    public function findMaster(int $id): ?Master
    {
        $query = <<< SQL
            SELECT *
            FROM master
            WHERE master.master_id = $id
        SQL;

        $statement = $this->connection->query($query);
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->createMasterFromRow($row);
        }

        return null;
    }

    function createMaster(string $firstName, string $lastName, string $phone): int
    {
        $query = <<< SQL
            INSERT INTO master (first_name, last_name, phone)
            VALUES (:first_name, :last_name, :phone)
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([
            ":first_name" => $firstName,
            ":last_name" => $lastName,
            ":phone" => $phone,
        ]);

        return (int)$this->connection->lastInsertId();
    }

    function editMaster(int $masterId, string $firstName, string $lastName, string $phone): int
    {
        $query = <<< SQL
            UPDATE master 
            SET first_name = :first_name,last_name =  :last_name,phone = :phone
            WHERE master_id = :id
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([
            ":id" => $masterId,
            ":first_name" => $firstName,
            ":last_name" => $lastName,
            ":phone" => $phone,
        ]);

        return (int)$this->connection->lastInsertId();
    }

    function deleteMaster(int $masterId): bool
    {
        $prepareQuery = <<< SQL
            SELECT appointment.appointment_id
            FROM appointment
            WHERE master_id = :id;
        SQL;
        $statement = $this->connection->prepare($prepareQuery);
        $statement->execute([
            ":id" => $masterId
        ]);

        if ($row = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            return false;
        }

        $query = <<< SQL
            DELETE FROM master
            WHERE master_id = :id
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([
            ":id" => $masterId
        ]);

        return true;
    }

    private function createMasterFromRow(array $row): Master
    {
        return new Master(
            id: (int)$row["master_id"],
            firstName: $row["first_name"],
            lastName: $row["last_name"],
            phone: $row["phone"],
            createdAt: new \DateTimeImmutable($row["created_at"]),
            updatedAt: new \DateTimeImmutable($row["updated_at"]),
        );
    }
}