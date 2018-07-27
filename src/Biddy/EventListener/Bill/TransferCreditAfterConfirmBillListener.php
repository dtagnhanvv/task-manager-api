<?php

namespace Biddy\EventListener\Bill;

use Biddy\Model\Core\BillInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Monolog\Logger;

class TransferCreditAfterConfirmBillListener
{
    private $changeFields = ['status'];

    /** @var Manager */
    protected $workerManager;

    /** @var Logger */
    private $logger;

    private $updateEntities = [];

    /**
     * TransferCreditAfterConfirmBillChangeListener constructor.
     * @param Manager $workerManager
     * @param Logger $logger
     */
    public function __construct(Manager $workerManager, Logger $logger)
    {
        $this->workerManager = $workerManager;
        $this->logger = $logger;
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof BillInterface || $entity->getStatus() != BillInterface::STATUS_CONFIRMED) {
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
        $this->transferCreditForConfirmBills();
    }

    /**
     *
     */
    private function transferCreditForConfirmBills()
    {
        $updateEntities = $this->updateEntities;
        $this->updateEntities = [];

        $ids = array_map(function ($item) {
            if ($item instanceof BillInterface) {
                return $item->getId();
            }
        }, $updateEntities);

        if (!empty($ids)) {
            $this->workerManager->transferCreditForConfirmBills($ids, TransferCreditAfterConfirmBillListener::class);
        }
    }
}