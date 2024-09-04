<?php
declare(strict_types=1);

namespace App\Database\Client;

use App\Database\Connection;
use App\Model\Client;
use App\Model\Repository\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findOne(int $id): ?Client
    {
        $query = <<< SQL
            SELECT client_id, first_name, last_name, phone, created_at, updated_at
            FROM client
            WHERE client.client_id = $id
        SQL;

        $statement = $this->connection->execute($query);
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->hydrateClient($row);
        }

        return null;
    }

    public function save(Client $client): int
    {
        $clientId = $client->getId();
        if ($clientId)
        {
            $this->updateClient($client);
        }
        else
        {
            $clientId = $this->insertClient($client);
            $client->assignIdentifier($clientId);
        }

        return $clientId;
    }

    private function hydrateClient(array $row): Client
    {
        try {
            return new Client(
                id: (int)$row["client_id"],
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

    private function insertClient(Client $client): int
    {
        $query = <<<SQL
            INSERT INTO client (first_name, last_name, phone)
            VALUES (:first_name, :last_name, :phone)
            SQL;
        $params = [
            ":first_name" => $client->getFirstName(),
            ":last_name" => $client->getLastName(),
            ":phone" => $client->getPhone(),
        ];

        $this->connection->execute($query, $params);

        return $this->connection->getLastInsertId();
    }

    private function updateClient(Client $client): void
    {
        $query = <<<SQL
            UPDATE client 
            SET first_name = :first_name,last_name =  :last_name,phone = :phone
            WHERE client_id = :id
            SQL;
        $params = [
            ":id" => $client->getId(),
            ":first_name" => $client->getFirstName(),
            ":last_name" => $client->getLastName(),
            ":phone" => $client->getPhone(),
        ];

        $stmt = $this->connection->execute($query, $params);
        if (!$stmt->rowCount())
        {
            throw new \Exception("Optimistic lock failed for article {$client->getId()}");
        }
    }
}