<?php

namespace Biddy\EventListener\Alert\Auction;

use Biddy\Entity\Core\Auction;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Service\Alert\ProcessAlertInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class CreateAlertForAuctionChangeListener
{
    private $newEntities = [];
    private $updateEntities = [];
    private $changeFields = ['minimumPrice', 'showBid', 'status', 'type', 'objective', 'incrementType', 'incrementValue', 'startDate', 'endDate'];

    /** @var Manager */
    protected $workerManager;

    /**
     * CreateAlertForProfileChangeListener constructor.
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

        if (!$entity instanceof AuctionInterface) {
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

        if (!$entity instanceof AuctionInterface) {
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
            if ($item instanceof AuctionInterface) {
                return $item->getId();
            }
        }, $newEntities);

        if (!empty($newIds)) {
            $this->workerManager->processAlert(AuctionInterface::class, $newIds, ProcessAlertInterface::ACTION_CREATE, CreateAlertForAuctionChangeListener::class);
        }
    }

    private function createAlertForUpdateEntities()
    {
        if (empty($this->updateEntities)) {
            return;
        }

        $updateEntities = $this->updateEntities;
        $this->updateEntities = [];

        $updatedIds = array_map(function ($item) {
            if ($item instanceof AuctionInterface) {
                return $item->getId();
            }
        }, $updateEntities);

        if (!empty($updatedIds)) {
            $this->workerManager->processAlert(AuctionInterface::class, $updatedIds, ProcessAlertInterface::ACTION_UPDATE, CreateAlertForAuctionChangeListener::class);
        }
    }
}