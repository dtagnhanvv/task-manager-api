<?php

namespace Biddy\EventListener\Credit;

use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class CreateWalletsForNewUserListener
{
    private $newEntities = [];
    private $updateEntities = [];

    /** @var Manager */
    protected $workerManager;

    /**
     * CreateWalletForAccountChangeListener constructor.
     * @param Manager $workerManager
     */
    public function __construct(Manager $workerManager)
    {
        $this->workerManager = $workerManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof UserRoleInterface) {
            return;
        }

        $this->newEntities[$entity->getId()] = $entity;
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof UserRoleInterface) {
            return;
        }

        $this->updateEntities[$entity->getId()] = $entity;
    }

    /**
     * @param PostFlushEventArgs $event
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $this->createWalletsForNewUser();
        $this->createWalletsForUpdateUser();
    }

    /**
     *
     */
    private function createWalletsForNewUser()
    {
        $newEntities = $this->newEntities;
        $this->newEntities = [];

        $ids = array_map(function ($item) {
            if ($item instanceof UserRoleInterface) {
                return $item->getId();
            }
        }, $newEntities);

        if (!empty($ids)) {
            $this->workerManager->createWalletsForUser($ids, CreateWalletsForNewUserListener::class);
        }
    }

    /**
     *
     */
    private function createWalletsForUpdateUser()
    {
        $updateEntities = $this->updateEntities;
        $this->updateEntities = [];

        $ids = array_map(function ($item) {
            if ($item instanceof UserRoleInterface) {
                return $item->getId();
            }
        }, $updateEntities);

        if (!empty($ids)) {
            $this->workerManager->createWalletsForUser($ids, CreateWalletsForNewUserListener::class);
        }
    }
}