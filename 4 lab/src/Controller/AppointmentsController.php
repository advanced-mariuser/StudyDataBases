<?php
declare(strict_types=1);

namespace App\Controller;

use App\Database\AppointmentTable;
use App\Database\ClientTable;
use App\Database\ConnectionProvider;
use App\Database\MasterTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AppointmentsController extends AbstractController
{
    private AppointmentTable $appointmentTable;
    private MasterTable $masterTable;
    private ClientTable $clientTable;

    public function __construct()
    {
        parent::__construct();
        $connection = ConnectionProvider::connectDatabase();
        $this->clientTable = new ClientTable($connection);
        $this->masterTable = new MasterTable($connection);
        $this->appointmentTable = new AppointmentTable($connection);
    }

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

        $masterFullName = null;
        $clientFullName = null;

        if (!is_null($clientId)) {
            $client = $this->clientTable->findClient((int)$clientId);
            $clientFullName = $client->getFirstName() . ' ' . $client->getLastName();
            $lastPlaceList[] = ['url' => 'clients', 'name' => 'Список клиентов'];
            $lastPlaceList[] = ['url' => "client/edit?client_id=$clientId", 'name' => 'Клиент'];
        }
        if (!is_null($masterId)) {
            $master = $this->masterTable->findMaster((int)$masterId);
            $masterFullName = $master->getFirstName() . ' ' . $master->getLastName();
            $lastPlaceList[] = ['url' => 'masters', 'name' => 'Список мастеров'];
            $lastPlaceList[] = ['url' => "master/edit?master_id=$masterId", 'name' => 'Мастер'];
        }

        $clients = $this->clientTable->getAllClients();
        $masters = $this->masterTable->getAllMasters();

        $body = $this->twig->render('new_appointment.twig', [
            'places' => $lastPlaceList,
            'title' => 'Новая запись',
            'master_id' => $masterId,
            'master_full_name' => $masterFullName ?? null,
            'client_id' => $clientId,
            'client_full_name' => $clientFullName ?? null,
            'masters' => $masters,
            'clients' => $clients,
        ]);
        return $this->success($response, $body);
    }

    public function createAppointment(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $parsedFields = $request->getParsedBody();
        $appointmentId = $this->appointmentTable->createAppointment((int) $parsedFields['client'],(int) $parsedFields['master'], new \DateTimeImmutable($parsedFields['date']));
        $body = "/appointment/edit?appointment_id=$appointmentId";

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
        $master = $queryParams['master_id'] ?? null;
        $client = $queryParams['client_id'] ?? null;
        if($master)
        {
            $id = $queryParams['master_id'];
            $lastPlaceList[] = ['url' => 'masters', 'name' => 'Список мастеров'];
            $lastPlaceList[] = ['url' => "master/edit?master_id=$id", 'name' => 'Мастер'];
        }
        elseif ($client)
        {
            $id = $queryParams['client_id'];
            $lastPlaceList[] = ['url' => 'clients', 'name' => 'Список клиентов'];
            $lastPlaceList[] = ['url' => "client/edit?client_id=$id", 'name' => 'Клиент'];
        }

        if (is_null($appointmentId)) {
            return $this->badRequest($response);
        }

        $appointment = $this->appointmentTable->findAppointment((int)$appointmentId);
        $clients = $this->clientTable->getAllClients();
        $masters = $this->masterTable->getAllMasters();

        $client = $this->clientTable->findClient($appointment->getClientId());
        if($client)
        {
            $clientFullName = $client->getFirstName() . ' ' . $client->getLastName();
        }
        $master = $this->masterTable->findMaster((int)$appointment->getMasterId());
        if($master)
        {
            $masterFullName = $master->getFirstName() . ' ' . $master->getLastName();
        }

        $body = $this->twig->render('edit_appoinment.twig', [
            'appoinment_id' => $appointmentId,
            'places' => $lastPlaceList ?? null,
            'title' => 'Запись',
            'master_id' => $appointment->getMasterId(),
            'master_full_name' => $masterFullName ?? null,
            'client_id' => $appointment->getClientId(),
            'client_full_name' => $clientFullName ?? null,
            'masters' => $masters,
            'clients' => $clients,
            'dateValue' => $appointment->getDate(),
        ]);
        return $this->success($response, $body);
    }

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

        $this->appointmentTable->editAppointment((int)$appointmentId, (int) $parsedFields['client'],(int) $parsedFields['master'], new \DateTimeImmutable($parsedFields['phone']));
        $body = "/appointment/edit?appointment_id=$appointmentId";

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

        if (!$this->appointmentTable->deleteAppointment((int)$appointmentId)) {
            return $this->badRequest($response);
        }
        $body = "/clients";

        return $this->redirect($response, $body);
    }
}