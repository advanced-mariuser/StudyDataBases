<?php
declare(strict_types=1);

namespace App\Model\Service\Master;

use App\Common\Database\TransactionalExecutor;
use App\Database\ConnectionProvider;
use App\Database\Master\MasterQueryService;
use App\Database\Master\MasterRepository;
use App\Model\Service\Master\MasterService;

final class MasterServiceProvider
{
    private ?MasterService $masterService = null;
    private ?MasterQueryService $masterQueryService = null;
    private ?MasterRepository $masterRepository = null;

    public static function getInstance(): self
    {
        static $instance = null;
        if ($instance === null)
        {
            $instance = new self();
        }
        return $instance;
    }

    public function getMasterService(): MasterService
    {
        if ($this->masterService === null)
        {
            $synchronization = new TransactionalExecutor(ConnectionProvider::getConnection());
            $this->masterService = new MasterService(
                $synchronization, $this->getMasterRepository());
        }
        return $this->masterService;
    }

    public function getMasterQueryService(): MasterQueryService
    {
        if ($this->masterQueryService === null)
        {
            $this->masterQueryService = new MasterQueryService(ConnectionProvider::getConnection());
        }
        return $this->masterQueryService;
    }

    private function getMasterRepository(): MasterRepository
    {
        if ($this->masterRepository === null)
        {
            $this->masterRepository = new MasterRepository(ConnectionProvider::getConnection());
        }
        return $this->masterRepository;
    }
}