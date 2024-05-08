<?php
declare(strict_types=1);

namespace App\Controller;

use App\Database\AppointmentTable;
use App\Database\ConnectionProvider;
use App\Database\ClientTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ClientsController extends AbstractController
{
    private ClientTable $clientTable;
    private AppointmentTable $appointmentTable;

    public function __construct()
    {
        parent::__construct();
        $connection = ConnectionProvider::connectDatabase();
        $this->clientTable = new ClientTable($connection);
        $this->appointmentTable = new AppointmentTable($connection);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function listClients(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }
        $clientsList = $this->clientTable->getAllClients();

        $body = $this->twig->render('list.twig', [
            'title' => 'Список клиентов',
            'list' => $clientsList,
            'person' => 'client'
        ]);
        return $this->success($response, $body);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function newClient(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $lastPlaceList[] = ['url' => 'clients', 'name' => 'Список клиентов'];

        $body = $this->twig->render('new_person.twig', [
            'places' => $lastPlaceList,
            'title' => 'Новый клиент',
            'person' => 'client'
        ]);
        return $this->success($response, $body);
    }

    public function createClient(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $parsedFields = $request->getParsedBody();
        $clientId = $this->clientTable->createClient($parsedFields['first_name'], $parsedFields['last_name'], $parsedFields['phone']);
        $body = "/client/edit?client_id=$clientId";

        return $this->redirect($response, $body);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function editClient(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $clientId = $queryParams['client_id'] ?? null;

        if (is_null($clientId)) {
            return $this->badRequest($response);
        }

        $lastPlaceList[] = ['url' => 'clients', 'name' => 'Список клиентов'];

        $appointments = $this->appointmentTable->getAllAppointments(null, (int) $clientId);

        $client = $this->clientTable->findClient((int)$clientId);
        $body = $this->twig->render('edit_person.twig', [
            'places' => $lastPlaceList,
            'title' => 'Клиент',
            'person' => 'client',
            'list' => $appointments,
            'person_id' => $client->getId(),
            'first_name' => $client->getFirstName(),
            'last_name' => $client->getLastName(),
            'phone' => $client->getPhone(),
            'created_at' => $client->getCreatedAt(),
            'updated_at' => $client->getUpdatedAt()
        ]);

        return $this->success($response, $body);
    }

    public function updateClient(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $clientId = $queryParams['client_id'] ?? null;

        if (is_null($clientId)) {
            return $this->badRequest($response);
        }

        $parsedFields = $request->getParsedBody();

        $this->clientTable->editClient((int)$clientId, $parsedFields['first_name'], $parsedFields['last_name'], $parsedFields['phone']);
        $body = "/client/edit?client_id=$clientId";

        return $this->redirect($response, $body);
    }

    public function deleteClient(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $clientId = $queryParams['client_id'] ?? null;

        if (is_null($clientId)) {
            return $this->badRequest($response);
        }

        if (!$this->clientTable->deleteClient((int)$clientId)) {
            return $this->badRequest($response);
        }
        $body = "/clients";

        return $this->redirect($response, $body);
    }
}
