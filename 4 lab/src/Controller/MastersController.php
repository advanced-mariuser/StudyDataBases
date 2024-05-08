<?php
declare(strict_types=1);

namespace App\Controller;

use App\Database\AppointmentTable;
use App\Database\ConnectionProvider;
use App\Database\MasterTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MastersController extends AbstractController
{
    private MasterTable $masterTable;
    private AppointmentTable $appointmentTable;

    public function __construct()
    {
        parent::__construct();
        $connection = ConnectionProvider::connectDatabase();
        $this->masterTable = new MasterTable($connection);
        $this->appointmentTable = new AppointmentTable($connection);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function listMasters(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }
        $mastersList = $this->masterTable->getAllMasters();

        $body = $this->twig->render('list.twig', [
            'title' => 'Список мастеров',
            'list' => $mastersList,
            'person' => 'master'
        ]);
        return $this->success($response, $body);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function newMaster(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $lastPlaceList[] = ['url' => 'masters', 'name' => 'Список мастеров'];

        $body = $this->twig->render('new_person.twig', [
            'places' => $lastPlaceList,
            'title' => 'Новый клиент',
            'person' => 'master'
        ]);
        return $this->success($response, $body);
    }

    public function createMaster(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $parsedFields = $request->getParsedBody();
        $masterId = $this->masterTable->createMaster($parsedFields['first_name'], $parsedFields['last_name'], $parsedFields['phone']);
        $body = "/master/edit?master_id=$masterId";

        return $this->redirect($response, $body);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function editMaster(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $masterId = $queryParams['master_id'] ?? null;

        if (is_null($masterId)) {
            return $this->badRequest($response);
        }

        $lastPlaceList[] = ['url' => 'masters', 'name' => 'Список мастеров'];

        $appointments = $this->appointmentTable->getAllAppointments((int) $masterId, null);

        $master = $this->masterTable->findMaster((int)$masterId);
        $body = $this->twig->render('edit_person.twig', [
            'places' => $lastPlaceList,
            'title' => 'Мастер',
            'person' => 'master',
            'list' => $appointments,
            'person_id' => $master->getId(),
            'first_name' => $master->getFirstName(),
            'last_name' => $master->getLastName(),
            'phone' => $master->getPhone(),
            'created_at' => $master->getCreatedAt(),
            'updated_at' => $master->getUpdatedAt()
        ]);

        return $this->success($response, $body);
    }

    public function updateMaster(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $masterId = $queryParams['master_id'] ?? null;

        if (is_null($masterId)) {
            return $this->badRequest($response);
        }

        $parsedFields = $request->getParsedBody();

        $this->masterTable->editMaster((int)$masterId, $parsedFields['first_name'], $parsedFields['last_name'], $parsedFields['phone']);
        $body = "/master/edit?master_id=$masterId";

        return $this->redirect($response, $body);
    }

    public function deleteMaster(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isPost($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $masterId = $queryParams['master_id'] ?? null;

        if (is_null($masterId)) {
            return $this->badRequest($response);
        }

        if (!$this->masterTable->deleteMaster((int)$masterId)) {
            return $this->badRequest($response);
        }
        $body = "/masters";

        return $this->redirect($response, $body);
    }
}
