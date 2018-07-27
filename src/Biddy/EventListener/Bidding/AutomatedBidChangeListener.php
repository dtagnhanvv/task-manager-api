<?php

namespace Biddy\EventListener\Bidding;

use Biddy\Entity\Core\Bid;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Service\Util\AuctionUtilTrait;
use Biddy\Service\Bidding\Automated\AutomatedAuctionRule;
use Biddy\Service\Bidding\Core\AuctionRuleInterface;
use Biddy\Service\Util\PublicSimpleException;
use Biddy\Worker\Manager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class AutomatedBidChangeListener
{
    use AuctionUtilTrait;

    /** @var Manager */
    protected $workerManager;

    /**
     * GuaranteeBidWhenBiddingListener constructor.
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
        $bid = $args->getEntity();
        $em = $args->getEntityManager();

        if (!$bid instanceof BidInterface) {
            return;
        }

        $this->verifyAuction($bid, $em);
    }

    /**
     * @param BidInterface $bid
     * @param EntityManagerInterface $em
     * @throws PublicSimpleException
     */
    private function verifyAuction(BidInterface $bid, EntityManagerInterface $em)
    {
        $auction = $bid->getAuction();

        if (!$auction instanceof AuctionInterface || $auction->getType() == AutomatedAuctionRule::AUTOMATED) {
            return;
        }

        $targetPrice = $auction->getMinimumPrice();

        switch ($auction->getType()) {
            case AuctionRuleInterface::MANUAL:
                break;
            case AuctionRuleInterface::AUTOMATED:
                switch ($auction->getObjective()) {
                    case AuctionInterface::OBJECTIVE_LOWEST_PRICE:
                        break;
                    case AuctionInterface::OBJECTIVE_HIGHEST_PRICE:
                        $targetPrice = $this->calculateTargetPriceForAuction($auction, $em);
                        break;
                }
                break;
        }

        if ($bid->getPrice() < $targetPrice) {
            throw new PublicSimpleException('Fail due to not enough price');
        }
    }

    /**
     * @param AuctionInterface $auction
     * @param EntityManagerInterface $em
     * @return mixed
     */
    private function calculateTargetPriceForAuction(AuctionInterface $auction, EntityManagerInterface $em)
    {
        $bidRepository = $em->getRepository(Bid::class);

        return $this->calculateNextPrice($auction, $bidRepository);
    }
}