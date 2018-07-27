<?php

namespace Biddy\EventListener\Alert\Credit;

use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Service\Alert\ProcessAlertInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

class CreateAlertForCreditTransactionChangeListener
{
    private $newEntities = [];

    /** @var Manager */
    protected $workerManager;

    /**
     * CreateAlertForAccountChangeListener constructor.
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

        if (!$entity instanceof CreditTransactionInterface) {
            return;
        }

        $fromWallet = $entity->getFromWallet();
        $targetWallet = $entity->getTargetWallet();

        if ($fromWallet->getOwner()->getId() == $targetWallet->getOwner()->getId()) {
            return;
        }

        $this->newEntities[$entity->getId()] = $entity;
    }

    /**
     * @param PostFlushEventArgs $event
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $this->createAlertForNewEntities();
    }

    private function createAlertForNewEntities()
    {
        if (empty($this->newEntities)) {
            return;
        }

        $newEntities = $this->newEntities;
        $this->newEntities = [];

        $newIds = array_map(function ($item) {
            if ($item instanceof CreditTransactionInterface) {
                return $item->getId();
            }
        }, $newEntities);

        if (!empty($newIds)) {
            $this->workerManager->processAlert(CreditTransactionInterface::class, $newIds, ProcessAlertInterface::ACTION_CREATE, CreateAlertForCreditTransactionChangeListener::class);
        }
    }
}