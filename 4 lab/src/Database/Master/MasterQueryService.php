<?php
declare(strict_types=1);

namespace App\Database\Master;

use App\Database\Connection;
use App\Model\Data\Master\MasterSummary;

class MasterQueryService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return MasterSummary[]
     */
    public function listMasters(): array
    {
        $query = <<< SQL
            SELECT master_id, first_name, last_name, phone, created_at, updated_at
            FROM master
            ORDER BY first_name
        SQL;

        $stmt = $this->connection->execute($query);

        return array_map(
            fn($row) => $this->hydrateMastersSummary($row),
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    private function hydrateMastersSummary(array $row): MasterSummary
    {
        try
        {
            return new MasterSummary(
                (int)$row['master_id'],
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