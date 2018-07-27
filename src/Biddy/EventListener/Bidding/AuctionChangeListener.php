<?php

namespace Biddy\EventListener\Bidding;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Service\Util\AuctionUtilTrait;
use Biddy\Worker\Manager;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class AuctionChangeListener
{
    use AuctionUtilTrait;

    /** @var Manager */
    protected $workerManager;

    private $updateEntities = [];

    private $changeFields = ['objective', 'minimumPrice'];

    /** @var EntityManagerInterface */
    private $em;

    /**
     * GuaranteeBidWhenBiddingListener constructor.
     * @param Manager $workerManager
     */
    public function __construct(Manager $workerManager)
    {
        $this->workerManager = $workerManager;
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
        $this->em = $event->getEntityManager();
        $this->createAlertForUpdateEntities();
    }

    private function createAlertForUpdateEntities()
    {
        if (empty($this->updateEntities)) {
            return;
        }

        $updateEntities = $this->updateEntities;
        $this->updateEntities = [];
        $count = 0;

        foreach ($updateEntities as $entity) {
            if (!$entity instanceof AuctionInterface) {
                continue;
            }

            $this->validateBidStatusOnAuction($entity);

            $count++;
        }

        if (!empty($count)) {
            $this->em->flush();
        }
    }

    /**
     * @param AuctionInterface $auction
     */
    private function validateBidStatusOnAuction(AuctionInterface $auction)
    {
        $bids = $auction->getBids();
        $bids = $bids instanceof Collection ? $bids->toArray() : [$bids];

        foreach ($bids as $bid) {
            if (!$bid instanceof BidInterface) {
                continue;
            }

            //Update for pending only
            if (!in_array($bid->getStatus(), [BidInterface::STATUS_BIDDING, BidInterface::STATUS_INVALID])) {
                continue;
            }

            if ($bid->getPrice() < $auction->getMinimumPrice()) {
                $bid->setStatus(BidInterface::STATUS_INVALID);
            } else {
                $bid->setStatus(BidInterface::STATUS_BIDDING);
            }

            $this->em->persist($bid);
        }
    }
}