<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Data\Client\CreateClientParams;
use App\Model\Data\Client\EditClientParams;
use App\Model\Service\Appointment\AppointmentServiceProvider;
use App\Model\Service\Client\ClientServiceProvider;
use App\Model\Service\Master\MasterServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ClientsController extends AbstractController
{
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
        $clientsList = ClientServiceProvider::getInstance()->getClientQueryService()->listClients();

        $title = 'Список клиентов';
        $bradCrumb[] = ['title' => $title, 'url' => 'clients'];

        $body = $this->twig->render('list.twig', [
            'bradCrumb' => $bradCrumb,
            'title' => $title,
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
    public function newClientForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $lastTitle = 'Список клиентов';
        $newTitle = 'Новый клиент';
        $bradCrumb = [
            ['title' => $lastTitle, 'url' => 'clients'],
            ['title' => $newTitle, 'url' => 'client/new']
        ];

        $body = $this->twig->render('new_person.twig', [
            'bradCrumb' => $bradCrumb,
            'title' => $newTitle,
            'person' => 'client'
        ]);
        return $this->success($response, $body);
    }

    public function createClientForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $parsedFields = $request->getParsedBody();
        $params = new CreateClientParams($parsedFields['first_name'], $parsedFields['last_name'], $parsedFields['phone']);
        $clientId = ClientServiceProvider::getInstance()->getClientService()->createClient($params);

        $body = "/client/edit?client_id=$clientId";

        return $this->redirect($response, $body);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function editClientForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $clientId = $queryParams['client_id'] ?? null;

        if (is_null($clientId)) {
            return $this->badRequest($response);
        }

        $client = ClientServiceProvider::getInstance()->getClientService()->getClient((int)$clientId);

        if (is_null($client)) {
            return $this->badRequest($response);
        }

        $lastTitle = 'Список клиентов';
        $newTitle = 'Клиент';
        $bradCrumb = [
            ['url' => 'clients', 'title' => $lastTitle],
            ['url' => "client/edit?client_id={$client->getId()}", 'title' => $newTitle]
        ];

        $appointments = AppointmentServiceProvider::getInstance()->getAppointmentQueryService()->listAppointments(null, (int)$clientId);

        $body = $this->twig->render('edit_person.twig', [
            'bradCrumb' => $bradCrumb,
            'title' => $newTitle,
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
        $editedClient = ClientServiceProvider::getInstance()->getClientService()->getClient((int)$clientId);

        if (is_null($editedClient)) {
            return $this->badRequest($response);
        }

        $params = new EditClientParams((int)$clientId, $parsedFields['first_name'], $parsedFields['last_name'],
            $parsedFields['phone']);
        ClientServiceProvider::getInstance()->getClientService()->editClient($params);

        $body = "/client/edit?client_id={$editedClient->getId()}";
        return $this->redirect($response, $body);
    }
}
