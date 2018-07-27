<?php

namespace Biddy\EventListener\Alert\Profile;

use Biddy\Model\User\Role\SaleInterface;
use Biddy\Service\Alert\ProcessAlertInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class CreateAlertForSaleChangeListener
{
    private $newEntities = [];
    private $updateEntities = [];
    private $changeFields = ['phone', 'email'];

    /** @var Manager */
    protected $workerManager;

    /**
     * CreateAlertForSaleChangeListener constructor.
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
        $em = $args->getEntityManager();

        if (!$entity instanceof SaleInterface) {
            return;
        }

        $this->newEntities[] = $entity;
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if (!$entity instanceof SaleInterface) {
            return;
        }

        if (count(array_intersect(array_keys($args->getEntityChangeSet()), $this->changeFields)) < 1) {
            return;
        }

        $this->updateEntities[] = $entity;
    }

    /**
     * @param PostFlushEventArgs $event
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $this->createAlertForNewEntities();
        $this->createAlertForUpdateEntities();
    }

    private function createAlertForNewEntities()
    {
        if (empty($this->newEntities)) {
            return;
        }

        $newEntities = $this->newEntities;
        $this->newEntities = [];

        $newIds = array_map(function ($item) {
            if ($item instanceof SaleInterface) {
                return $item->getId();
            }
        }, $newEntities);

        if (!empty($newIds)) {
            $this->workerManager->processAlert(SaleInterface::class, $newIds, ProcessAlertInterface::ACTION_CREATE, CreateAlertForSaleChangeListener::class);
        }
    }

    private function createAlertForUpdateEntities()
    {
        $updateEntities = $this->updateEntities;
        $this->updateEntities = [];

        $ids = array_map(function ($item) {
            if ($item instanceof SaleInterface) {
                return $item->getId();
            }
        }, $updateEntities);

        if (!empty($ids)) {
            $this->workerManager->processAlert(SaleInterface::class, $ids, ProcessAlertInterface::ACTION_UPDATE, CreateAlertForSaleChangeListener::class);
        }
    }
}