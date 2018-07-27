<?php

namespace Biddy\EventListener\Bill;

use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\BillInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Monolog\Logger;

class TransferCreditAfterRejectBillListener
{
    private $changeFields = ['status'];

    /** @var Manager */
    protected $workerManager;

    /** @var EntityManagerInterface */
    private $em;

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

        if (!$entity instanceof BillInterface || $entity->getStatus() != BillInterface::STATUS_REJECTED) {
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
        $this->em = $event->getEntityManager();
        $this->transferCreditForConfirmBills();
    }

    /**
     *
     */
    private function transferCreditForConfirmBills()
    {
        $updateEntities = $this->updateEntities;
        $this->updateEntities = [];
        $count = 0;
        foreach ($updateEntities as $entity) {
            if (!$entity instanceof BillInterface) {
                continue;
            }

            $bid = $entity->getBid();
            $bid->setStatus(BidInterface::STATUS_REJECTED);
            $this->em->persist($bid);
            $count++;
        }

        if (!empty($count)) {
            $this->em->flush();
        }
    }
}