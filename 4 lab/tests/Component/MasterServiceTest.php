<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Common\Database\TransactionalExecutor;
use App\Database\Master\MasterRepository;
use App\Model\Data\Master\CreateMasterParams;
use App\Model\Data\Master\EditMasterParams;
use App\Model\Exception\MasterNotFoundException;
use App\Model\Service\Master\MasterService;
use App\Tests\Common\AbstractDatabaseTestCase;

class MasterServiceTest extends AbstractDatabaseTestCase
{
    public function testCreateEditAndDeleteClient(): void
    {
        $service = $this->createMasterService();

        $masterId = $service->createMaster(new CreateMasterParams(
            firstName: 'Иван',
            lastName: 'Иванович',
            phone: '+79997776655'
        ));

        $master = $service->getMaster($masterId);
        $this->assertEquals('Иван Иванович', $master->getFullName());
        $this->assertEquals('+79997776655', $master->getPhone());

        $service->editMaster(new EditMasterParams(
                id: $masterId,
                firstName: 'Ваня',
                lastName: 'Ванович',
                phone: '+78887776655'
            )
        );

        $master = $service->getMaster($masterId);
        $this->assertEquals('Ваня Ванович', $master->getFullName());
        $this->assertEquals('+78887776655', $master->getPhone());

        $service->deleteMaster($masterId);

        $this->assertThrows(
            static fn() => $service->getMaster($masterId),
            MasterNotFoundException::class
        );
    }

    private function assertThrows(\Closure $closure, string $exceptionClass): void
    {
        $actualExceptionClass = null;
        try
        {
            $closure();
        }
        catch (\Throwable $e)
        {
            $actualExceptionClass = $e::class;
        }
        $this->assertEquals($exceptionClass, $actualExceptionClass, "$exceptionClass exception should be thrown");
    }

    private function createMasterService(): MasterService
    {
        $connection = $this->getConnection();
        return new MasterService(
            new TransactionalExecutor($connection),
            new MasterRepository($connection)
        );
    }
}