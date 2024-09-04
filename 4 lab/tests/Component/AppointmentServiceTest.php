<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Common\Database\TransactionalExecutor;
use App\Database\Appointment\AppointmentRepository;
use App\Database\Client\ClientRepository;
use App\Database\Master\MasterRepository;
use App\Model\Data\Appointment\CreateAppointmentParams;
use App\Model\Data\Client\CreateClientParams;
use App\Model\Data\Master\CreateMasterParams;
use App\Model\Service\Appointment\AppointmentService;
use App\Model\Service\Client\ClientService;
use App\Model\Service\Master\MasterService;
use App\Tests\Common\AbstractDatabaseTestCase;

class AppointmentServiceTest extends AbstractDatabaseTestCase
{
    public function testCreateAppointment(): void
    {
        $clientService = $this->createClientService();
        $masterService = $this->createMasterService();
        $appointmentService = $this->createAppointmentService();

        $clientId = $clientService->createClient(new CreateClientParams(
            firstName: 'Иван',
            lastName: 'Иванович',
            phone: '+78212416648'
        ));

       $masterId = $masterService->createMaster(new CreateMasterParams(
           firstName: 'Максим',
           lastName: 'Максимов',
           phone: '+79996667755'
       ));

       $appointmentId = $appointmentService->createAppointment(new CreateAppointmentParams(
           masterId: $masterId,
           clientId: $clientId,
           date: new \DateTimeImmutable('2024-01-01 14:14:00')
       ));

        $appointment = $appointmentService->getAppointment($appointmentId);
        $this->assertEquals($masterId, $appointment->getMasterId());
        $this->assertEquals($clientId, $appointment->getClientId());
        $this->assertEquals(new \DateTimeImmutable('2024-01-01 14:14:00'), $appointment->getDate());
    }

    private function createClientService(): ClientService
    {
        $connection = $this->getConnection();
        return new ClientService(
            new TransactionalExecutor($connection),
            new ClientRepository($connection)
        );
    }

    private function createMasterService(): MasterService
    {
        $connection = $this->getConnection();
        return new MasterService(
            new TransactionalExecutor($connection),
            new MasterRepository($connection)
        );
    }

    private function createAppointmentService(): AppointmentService
    {
        $connection = $this->getConnection();
        return new AppointmentService(
            new TransactionalExecutor($connection),
            new AppointmentRepository($connection)
        );
    }
}