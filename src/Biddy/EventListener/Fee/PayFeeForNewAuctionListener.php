<?php

namespace Biddy\EventListener\Fee;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

class PayFeeForNewAuctionListener
{
    /** @var Manager */
    protected $workerManager;
    private $newEntities = [];

    /**
     * PayFeeForNewAuctionListener constructor.
     * @param Manager $workerManager
     * @param $feeCredit
     * @param $offline
     */
    public function __construct(Manager $workerManager, $feeCredit, $offline)
    {
        $this->workerManager = $workerManager;
        $this->feeCredit = $feeCredit;
        $this->feeOffline = $offline;
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
     * @param PostFlushEventArgs $event
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $this->payFeeForNewAuctions();
    }

    /**
     *
     */
    private function payFeeForNewAuctions()
    {
        $newEntities = $this->newEntities;
        $this->newEntities = [];

        $ids = array_map(function ($item) {
            if ($item instanceof AuctionInterface) {
                return $item->getId();
            }
        }, $newEntities);

        if (!empty($ids)) {
            $this->workerManager->payFeeForNewAuctions($ids, PayFeeForNewAuctionListener::class);
        }
    }
}