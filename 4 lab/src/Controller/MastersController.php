<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Data\Master\CreateMasterParams;
use App\Model\Data\Master\EditMasterParams;
use App\Model\Service\Appointment\AppointmentServiceProvider;
use App\Model\Service\Master\MasterServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MastersController extends AbstractController
{
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

        $mastersList = MasterServiceProvider::getInstance()->getMasterQueryService()->listMasters();

        $title = 'Список мастеров';
        $bradCrumb[] = ['title' => $title, 'url' => 'masters'];

        $body = $this->twig->render('list.twig', [
            'bradCrumb' => $bradCrumb,
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
    public function newMasterForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $lastTitle = 'Список мастеров';
        $newTitle = 'Новый мастер';
        $bradCrumb = [
            ['title' => $lastTitle, 'url' => 'masters'],
            ['title' => $newTitle, 'url' => 'master/new']
        ];

        $body = $this->twig->render('new_person.twig', [
            'bradCrumb' => $bradCrumb,
            'title' => $newTitle,
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
        $params = new CreateMasterParams($parsedFields['first_name'], $parsedFields['last_name'], $parsedFields['phone']);
        $masterId = MasterServiceProvider::getInstance()->getMasterService()->createMaster($params);

        $body = "/master/edit?master_id=$masterId";
        return $this->redirect($response, $body);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function editMasterForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $queryParams = $request->getQueryParams();
        $masterId = $queryParams['master_id'] ?? null;

        if (is_null($masterId)) {
            return $this->badRequest($response);
        }

        $master = MasterServiceProvider::getInstance()->getMasterService()->getMaster((int)$masterId);

        if (is_null($master)) {
            return $this->badRequest($response);
        }

        $lastTitle = 'Список мастеров';
        $newTitle = 'Мастер';
        $bradCrumb = [
            ['url' => 'masters', 'title' => $lastTitle],
            ['url' => "master/edit?master_id={$master->getId()}", 'title' => $newTitle]
        ];

        $appointments = AppointmentServiceProvider::getInstance()->getAppointmentQueryService()->listAppointments((int)$masterId, null);

        $body = $this->twig->render('edit_person.twig', [
            'bradCrumb' => $bradCrumb,
            'title' => $newTitle,
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
        $editedMaster = MasterServiceProvider::getInstance()->getMasterService()->getMaster((int)$masterId);
        if (is_null($editedMaster)) {
            return $this->badRequest($response);
        }

        $params = new EditMasterParams((int)$masterId, $parsedFields['first_name'], $parsedFields['last_name'], $parsedFields['phone']);
        MasterServiceProvider::getInstance()->getMasterService()->editMaster($params);

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

        $editedMaster = MasterServiceProvider::getInstance()->getMasterService()->getMaster((int)$masterId);
        if (is_null($editedMaster)) {
            return $this->badRequest($response);
        }

        if (!MasterServiceProvider::getInstance()->getMasterService()->deleteMaster((int)$masterId)) {
            return $this->badRequest($response);
        }

        $body = "/masters";
        return $this->redirect($response, $body);
    }
}
