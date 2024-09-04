<?php
declare(strict_types=1);

namespace App\Model\Service\Client;

use App\Common\Database\TransactionalExecutor;
use App\Database\Client\ClientQueryService;
use App\Database\Client\ClientRepository;
use App\Database\ConnectionProvider;

final class ClientServiceProvider
{
    private ?ClientService $clientService = null;
    private ?ClientQueryService $clientQueryService = null;
    private ?ClientRepository $clientRepository = null;

    public static function getInstance(): self
    {
        static $instance = null;
        if ($instance === null)
        {
            $instance = new self();
        }
        return $instance;
    }

    public function getClientService(): ClientService
    {
        if ($this->clientService === null)
        {
            $synchronization = new TransactionalExecutor(ConnectionProvider::getConnection());
            $this->clientService = new ClientService(
                $synchronization, $this->getClientRepository());
        }
        return $this->clientService;
    }

    public function getClientQueryService(): ClientQueryService
    {
        if ($this->clientQueryService === null)
        {
            $this->clientQueryService = new ClientQueryService(ConnectionProvider::getConnection());
        }
        return $this->clientQueryService;
    }

    private function getClientRepository(): ClientRepository
    {
        if ($this->clientRepository === null)
        {
            $this->clientRepository = new ClientRepository(ConnectionProvider::getConnection());
        }
        return $this->clientRepository;
    }
}