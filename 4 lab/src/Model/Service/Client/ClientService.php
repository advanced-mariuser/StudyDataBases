<?php
declare(strict_types=1);

namespace App\Model\Service\Client;

use App\Common\Database\TransactionalExecutorInterface;
use App\Model\Client;
use App\Model\Data\Client\CreateClientParams;
use App\Model\Data\Client\EditClientParams;
use App\Model\Exception\ClientNotFoundException;
use App\Model\Repository\ClientRepositoryInterface;

readonly class ClientService
{
    public function __construct(
        private TransactionalExecutorInterface $transactionalExecutor,
        private ClientRepositoryInterface $clientRepository
    )
    {
    }

    /**
     * @param int $id
     * @return Client
     * @throws ClientNotFoundException
     */
    public function getClient(int $id): Client
    {
        $client = $this->clientRepository->findOne($id);
        if (!$client)
        {
            throw new ClientNotFoundException("Cannot find article with id $id");
        }
        return $client;
    }

    public function createClient(CreateClientParams $params): int
    {
        return $this->transactionalExecutor->doWithTransaction(function () use ($params) {
            $client = new Client(
                null,
                $params->getFirstName(),
                $params->getLastName(),
                $params->getPhone(),
            );
            return $this->clientRepository->save($client);
        });

    }

    /**
     * @param EditClientParams $params
     * @return void
     */
    public function editClient(EditClientParams $params): void
    {
        $this->transactionalExecutor->doWithTransaction(function () use ($params) {
            $client = $this->getClient($params->getId());
            $client->edit($params->getFirstName(), $params->getLastName(), $params->getPhone());
            $this->clientRepository->save($client);
        });
    }
}