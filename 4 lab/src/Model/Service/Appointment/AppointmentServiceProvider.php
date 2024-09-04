<?php
declare(strict_types=1);

namespace App\Model\Service\Appointment;

use App\Common\Database\TransactionalExecutor;
use App\Database\Appointment\AppointmentQueryService;
use App\Database\Appointment\AppointmentRepository;
use App\Database\ConnectionProvider;

final class AppointmentServiceProvider
{
    private ?AppointmentService $appointmentService = null;
    private ?AppointmentQueryService $appointmentQueryService = null;
    private ?AppointmentRepository $appointmentRepository = null;

    public static function getInstance(): self
    {
        static $instance = null;
        if ($instance === null)
        {
            $instance = new self();
        }
        return $instance;
    }

    public function getAppointmentService(): AppointmentService
    {
        if ($this->appointmentService === null)
        {
            $synchronization = new TransactionalExecutor(ConnectionProvider::getConnection());
            $this->appointmentService = new AppointmentService(
                $synchronization, $this->getAppointmentRepository());
        }
        return $this->appointmentService;
    }

    public function getAppointmentQueryService(): AppointmentQueryService
    {
        if ($this->appointmentQueryService === null)
        {
            $this->appointmentQueryService = new AppointmentQueryService(ConnectionProvider::getConnection());
        }
        return $this->appointmentQueryService;
    }

    private function getAppointmentRepository(): AppointmentRepository
    {
        if ($this->appointmentRepository === null)
        {
            $this->appointmentRepository = new AppointmentRepository(ConnectionProvider::getConnection());
        }
        return $this->appointmentRepository;
    }
}