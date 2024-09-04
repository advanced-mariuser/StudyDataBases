<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Common\Database\TransactionalExecutor;
use App\Database\Client\ClientRepository;
use App\Model\Data\Client\CreateClientParams;
use App\Model\Data\Client\EditClientParams;
use App\Model\Service\Client\ClientService;
use App\Tests\Common\AbstractDatabaseTestCase;

class ClientServiceTest extends AbstractDatabaseTestCase
{
    public function testCreateAndEditClient(): void
    {
        $service = $this->createClientService();

        $clientId = $service->createClient(new CreateClientParams(
            firstName: 'Иван',
            lastName: 'Иванович',
            phone: '+78212416648'
        ));

        $client = $service->getClient($clientId);
        $this->assertEquals('Иван Иванович', $client->getFullName());
        $this->assertEquals('+78212416648', $client->getPhone());

        $service->editClient(new EditClientParams(
                id: $clientId,
                firstName: 'Ваня',
                lastName: 'Ванович',
                phone: '+78212415648'
            )
        );

        $client = $service->getClient($clientId);
        $this->assertEquals('Ваня Ванович', $client->getFullName());
        $this->assertEquals('+78212415648', $client->getPhone());
    }

    private function createClientService(): ClientService
    {
        $connection = $this->getConnection();
        return new ClientService(
            new TransactionalExecutor($connection),
            new ClientRepository($connection)
        );
    }
}