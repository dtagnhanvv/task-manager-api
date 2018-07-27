<?php

namespace Biddy\EventListener\Fee;

use Biddy\Model\Core\BillInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Monolog\Logger;

class PayFeeForNewBillListener
{
    /** @var Manager */
    protected $workerManager;

    /** @var Logger */
    private $logger;

    private $newEntities = [];

    /**
     * PayFeeForNewBillListener constructor.
     * @param Manager $workerManager
     * @param Logger $logger
     */
    public function __construct(Manager $workerManager, Logger $logger)
    {
        $this->workerManager = $workerManager;
        $this->logger = $logger;
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
     * @param PostFlushEventArgs $event
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $this->transferCreditForNewBills();
    }

    /**
     *
     */
    private function transferCreditForNewBills()
    {
        $newEntities = $this->newEntities;
        $this->newEntities = [];

        $ids = array_map(function ($item) {
            if ($item instanceof BillInterface) {
                return $item->getId();
            }
        }, $newEntities);

        if (!empty($ids)) {
            $this->workerManager->payFeeForConfirmBillWorker($ids, PayFeeForNewBillListener::class);
        }
    }
}