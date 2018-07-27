<?php

namespace Biddy\EventListener\Alert\Bill;

use Biddy\Model\Core\BillInterface;
use Biddy\Service\Alert\ProcessAlertInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class CreateAlertForBillChangeListener
{
    private $newEntities = [];
    private $updateEntities = [];
    private $changeFields = ['payment', 'status', '	noteForSeller'];

    /** @var Manager */
    protected $workerManager;

    /**
     * CreateAlertForBillChangeListener constructor.
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

        if (!$entity instanceof BillInterface) {
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

        if (!$entity instanceof BillInterface) {
            return;
        }

        if (count(array_intersect(array_keys($args->getEntityChangeSet()), $this->changeFields)) < 1) {
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
            if ($item instanceof BillInterface) {
                return $item->getId();
            }
        }, $newEntities);

        if (!empty($newIds)) {
            $this->workerManager->processAlert(BillInterface::class, $newIds, ProcessAlertInterface::ACTION_CREATE, CreateAlertForBillChangeListener::class);
        }
    }

    private function createAlertForUpdateEntities()
    {
        $updateEntities = $this->updateEntities;
        $this->updateEntities = [];

        $ids = array_map(function ($item) {
            if ($item instanceof BillInterface) {
                return $item->getId();
            }
        }, $updateEntities);

        if (!empty($ids)) {
            $this->workerManager->processAlert(BillInterface::class, $ids, ProcessAlertInterface::ACTION_UPDATE, CreateAlertForBillChangeListener::class);
        }
    }
}