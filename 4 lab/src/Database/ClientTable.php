<?php
declare(strict_types=1);

namespace App\Database;

use App\Model\Client;
class ClientTable
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }


    /**
     * @return Client[]
     */
    public function getAllClients(): array
    {
        $query = <<< SQL
            SELECT *
            FROM client
        SQL;

        $statement = $this->connection->query($query);
        $clients = [];
        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $clients[] = $this->createClientFromRow($row);
        }

        return $clients;
    }

    public function findClient(int $id): ?Client
    {
        $query = <<< SQL
            SELECT *
            FROM client
            WHERE client.client_id = $id
        SQL;

        $statement = $this->connection->query($query);
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->createClientFromRow($row);
        }

        return null;
    }

    function createClient(string $firstName, string $lastName, string $phone): int
    {
        $query = <<< SQL
            INSERT INTO client (first_name, last_name, phone)
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

    function editClient(int $clientId, string $firstName, string $lastName, string $phone): void
    {
        $query = <<< SQL
            UPDATE client 
            SET first_name = :first_name,last_name =  :last_name,phone = :phone
            WHERE client_id = :id
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([
            ":id" => $clientId,
            ":first_name" => $firstName,
            ":last_name" => $lastName,
            ":phone" => $phone,
        ]);
    }

    private function createClientFromRow(array $row): Client
    {
        return new Client(
            id: (int)$row["client_id"],
            firstName: $row["first_name"],
            lastName: $row["last_name"],
            phone: $row["phone"],
            createdAt: new \DateTimeImmutable($row["created_at"]),
            updatedAt: new \DateTimeImmutable($row["updated_at"]),
        );
    }
}