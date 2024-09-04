<?php
declare(strict_types=1);

namespace App\Model\Service\Appointment;

use App\Common\Database\TransactionalExecutorInterface;
use App\Model\Appointment;
use App\Model\Client;
use App\Model\Data\Appointment\CreateAppointmentParams;
use App\Model\Data\Appointment\EditAppointmentParams;
use App\Model\Data\Client\CreateClientParams;
use App\Model\Data\Client\EditClientParams;
use App\Model\Exception\ClientNotFoundException;
use App\Model\Repository\AppointmentRepositoryInterface;

readonly class AppointmentService
{
    public function __construct(
        private TransactionalExecutorInterface $transactionalExecutor,
        private AppointmentRepositoryInterface $appointmentRepository
    )
    {
    }

    /**
     * @param int $id
     * @return Appointment
     * @throws ClientNotFoundException
     */
    public function getAppointment(int $id): Appointment
    {
        $appointment = $this->appointmentRepository->findOne($id);
        if (!$appointment)
        {
            throw new ClientNotFoundException("Cannot find article with id $id");
        }
        return $appointment;
    }

    public function createAppointment(CreateAppointmentParams $params): int
    {
        return $this->transactionalExecutor->doWithTransaction(function () use ($params) {
            $client = new Appointment(
                null,
                $params->getMasterId(),
                $params->getClientId(),
                $params->getDate(),
            );
            return $this->appointmentRepository->save($client);
        });

    }

    /**
     * @param EditAppointmentParams $params
     * @return void
     */
    public function editAppointment(EditAppointmentParams $params): void
    {
        $this->transactionalExecutor->doWithTransaction(function () use ($params) {
            $appointment = $this->getAppointment($params->getId());
            $appointment->edit($params->getMasterId(), $params->getClientId(), $params->getDate());
            $this->appointmentRepository->save($appointment);
        });
    }
}