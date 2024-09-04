<?php
declare(strict_types=1);

namespace App\Database\Client;

use App\Database\Connection;
use App\Model\Data\Client\ClientSummary;

class ClientQueryService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return ClientSummary[]
     */
    public function listClients(): array
    {
        $query = <<< SQL
            SELECT client_id, first_name, last_name, phone, created_at, updated_at
            FROM client
            ORDER BY first_name
        SQL;

        $stmt = $this->connection->execute($query);

        return array_map(
            fn($row) => $this->hydrateClientsSummary($row),
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    private function hydrateClientsSummary(array $row): ClientSummary
    {
        try
        {
            return new ClientSummary(
                (int)$row['client_id'],
                (string)$row['first_name'],
                (string)$row['last_name'],
            );
        }
        catch (\Exception $e)
        {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}