<?php
declare(strict_types=1);

namespace App\Controller;

use App\Database\AppointmentRepository;
use App\Database\Client\ClientRepository;
use App\Database\ConnectionProvider;
use App\Database\MasterRepository;
use App\Model\Appointment;
use App\Model\Data\Appointment\CreateAppointmentParams;
use App\Model\Data\Client\CreateClientParams;
use App\Model\Service\Appointment\AppointmentServiceProvider;
use App\Model\Service\Client\ClientServiceProvider;
use App\Model\Service\Master\MasterServiceProvider;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AppointmentsController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function newAppointmentForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $clientId = $queryParams['client_id'] ?? null;
        $masterId = $queryParams['master_id'] ?? null;

        if ((is_null($masterId) && is_null($clientId))) {
            return $this->badRequest($response);
        }

        if (!is_null($clientId)) {
            $client = ClientServiceProvider::getInstance()->getClientService()->getClient((int)$clientId);
            if (is_null($client)) {
                return $this->badRequest($response);
            }

            $bradCrumb = [
                ['url' => 'clients', 'title' => 'Список клиентов'],
                ['url' => "client/edit?client_id={$client->getId()}", 'title' => 'Клиент'],
                ['url' => "appointment/new?client_id={$client->getId()}", 'title' => 'Новая запись']
            ];
        }
        if (!is_null($masterId)) {
            $master = MasterServiceProvider::getInstance()->getMasterService()->getMaster((int)$masterId);
            if (is_null($master)) {
                return $this->badRequest($response);
            }

            $bradCrumb = [
                ['url' => 'masters', 'title' => 'Список мастеров'],
                ['url' => "master/edit?master_id={$master->getId()}", 'title' => 'Мастер'],
                ['url' => "appointment/new?master_id={$master->getId()}", 'title' => 'Новая запись']
            ];
        }

        $clients = ClientServiceProvider::getInstance()->getClientQueryService()->listClients();
        $masters = MasterServiceProvider::getInstance()->getMasterQueryService()->listMasters();

        $body = $this->twig->render('new_appointment.twig', [
            'bradCrumb' => $bradCrumb,
            'title' => 'Новая запись',
            'master_id' => $masterId,
            'client_id' => $clientId,
            'masters' => $masters,
            'clients' => $clients,
        ]);
        return $this->success($response, $body);
    }

    /**
     * @throws Exception
     */
    public function createAppointment(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $parsedFields = $request->getParsedBody();
        $params = new CreateAppointmentParams(
            (int)$parsedFields['master'],
            (int)$parsedFields['client'],
            new \DateTimeImmutable($parsedFields['date']));
        $appointmentId = AppointmentServiceProvider::getInstance()->getAppointmentService()->createAppointment($params);

        $maseterId = (int)$parsedFields['master'];
        $body = "/appointment/edit?appointment_id=$appointmentId&master_id=$maseterId";
        return $this->redirect($response, $body);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function editAppointmentForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $appointmentId = $queryParams['appointment_id'] ?? null;
        $masterId = $queryParams['master_id'] ?? null;
        $clientId = $queryParams['client_id'] ?? null;

        if (is_null($masterId) && is_null($clientId) && is_null($appointmentId)) {
            return $this->badRequest($response);
        }

        if ($masterId) {
            $master = MasterServiceProvider::getInstance()->getMasterService()->getMaster((int)$masterId);
            if (is_null($master)) {
                return $this->badRequest($response);
            }

            $bradCrumb = [
                ['url' => 'masters', 'title' => 'Список мастеров'],
                ['url' => "master/edit?master_id={$master->getId()}", 'title' => 'Мастер'],
                ['url' => "appointment/edit?appointment_id=$appointmentId&master_id={$master->getId()}", 'title' => 'Запись']
            ];
        } elseif ($clientId) {
            $client = ClientServiceProvider::getInstance()->getClientService()->getClient((int)$clientId);
            if (is_null($client)) {
                return $this->badRequest($response);
            }

            $bradCrumb = [
                ['url' => 'clients', 'title' => 'Список клиентов'],
                ['url' => "client/edit?client_id={$client->getId()}", 'title' => 'Клиент'],
                ['url' => "appointment/edit?appointment_id=$appointmentId&client_id={$client->getId()}", 'title' => 'Запись']
            ];
        }

        if (is_null($appointmentId)) {
            return $this->badRequest($response);
        }

        $appointment = AppointmentServiceProvider::getInstance()->getAppointmentService()->getAppointment((int)$appointmentId);
        $clients = ClientServiceProvider::getInstance()->getClientQueryService()->listClients();
        $masters = MasterServiceProvider::getInstance()->getMasterQueryService()->listMasters();

        $body = $this->twig->render('edit_appoinment.twig', [
            'bradCrumb' => $bradCrumb,
            'appoinment_id' => $appointmentId,
            'title' => 'Запись',
            'master_id' => $appointment->getMasterId(),
            'client_id' => $appointment->getClientId(),
            'masters' => $masters,
            'clients' => $clients,
            'dateValue' => $appointment->getDate(),
        ]);
        return $this->success($response, $body);
    }

    /**
     * @throws Exception
     */
    public function updateAppointment(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $appointmentId = $queryParams['appointment_id'] ?? null;

        if (is_null($appointmentId)) {
            return $this->badRequest($response);
        }

        $parsedFields = $request->getParsedBody();
        $editedAppointment = $this->appointmentRepository->findAppointmentById((int)$appointmentId);

        if (is_null($editedAppointment)) {
            return $this->badRequest($response);
        }

        $editedAppointment->edit((int)$parsedFields['master'], (int)$parsedFields['client'], new \DateTimeImmutable($parsedFields['date']));
        $this->appointmentRepository->editAppointment($editedAppointment);

        $body = "/appointment/edit?appointment_id=$appointmentId&master_id={$editedAppointment->getMasterId()}&client_id={$editedAppointment->getClientId()}";
        return $this->redirect($response, $body);
    }

    public function deleteAppointment(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $appointmentId = $queryParams['appointment_id'] ?? null;

        if (is_null($appointmentId)) {
            return $this->badRequest($response);
        }

        $appointment = $this->appointmentRepository->findAppointmentById((int)$appointmentId);

        if (is_null($appointment)) {
            return $this->badRequest($response);
        }

        if (!$this->appointmentRepository->deleteAppointment($appointment)) {
            return $this->badRequest($response);
        }

        $body = "/clients";
        return $this->redirect($response, $body);
    }
}