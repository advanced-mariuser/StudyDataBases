<?php
declare(strict_types=1);

namespace App\Model\Service\Master;

use App\Common\Database\TransactionalExecutorInterface;
use App\Model\Data\Client\CreateClientParams;
use App\Model\Data\Master\CreateMasterParams;
use App\Model\Data\Master\EditMasterParams;
use App\Model\Exception\MasterNotFoundException;
use App\Model\Master;
use App\Model\Repository\MasterRepositoryInterface;

readonly class MasterService
{
    public function __construct(
        private TransactionalExecutorInterface $transactionalExecutor,
        private MasterRepositoryInterface $masterRepository,
    )
    {
    }

    /**
     * @param int $id
     * @return Master
     * @throws MasterNotFoundException
     */
    public function getMaster(int $id): Master
    {
        $master = $this->masterRepository->findOne($id);
        if (!$master)
        {
            throw new MasterNotFoundException("Cannot find article with id $id");
        }
        return $master;
    }

    public function createMaster(CreateMasterParams $params): int
    {
        return $this->transactionalExecutor->doWithTransaction(function () use ($params) {
            $master = new Master(
                null,
                $params->getFirstName(),
                $params->getLastName(),
                $params->getPhone(),
            );
            return $this->masterRepository->save($master);
        });

    }

    /**
     * @param EditMasterParams $params
     * @return void
     */
    public function editMaster(EditMasterParams $params): void
    {
        $this->transactionalExecutor->doWithTransaction(function () use ($params) {
            $client = $this->getMaster($params->getId());
            $client->edit($params->getFirstName(), $params->getLastName(), $params->getPhone());
            $this->masterRepository->save($client);
        });
    }

    public function deleteMaster(int $id): bool
    {
        return $this->masterRepository->delete($id);
    }
}