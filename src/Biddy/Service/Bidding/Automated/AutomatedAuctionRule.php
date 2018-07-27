<?php

namespace Biddy\Service\Bidding\Automated;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Repository\Core\BidRepositoryInterface;
use Biddy\Service\Bidding\Core\AuctionRuleInterface;

class AutomatedAuctionRule implements AuctionRuleInterface
{
    //Configurable list
    private $supportActions;

    /** @var BidRepositoryInterface */
    private $bidRepository;

    /**
     * AutomatedAuctionRule constructor.
     * @param $supportRules
     * @param BidRepositoryInterface $bidRepository
     */
    public function __construct($supportRules, BidRepositoryInterface $bidRepository)
    {
        $this->supportActions = $supportRules;
        $this->bidRepository = $bidRepository;
    }

    /**
     * @inheritdoc
     */
    public function supportProduct(AuctionInterface $auction, $action = AuctionRuleInterface::AUTOMATED)
    {
        return in_array($action, $this->supportActions);
    }

    /**
     * @inheritdoc
     */
    public function getWinningBids(AuctionInterface $auction, $params = [])
    {
        if ($auction->getType() != AutomatedAuctionRule::AUTOMATED) {
            return null;
        }

        switch ($auction->getObjective()) {
            case AuctionInterface::OBJECTIVE_LOWEST_PRICE:
                return $this->bidRepository->getLowestPriceBid($auction);
            case AuctionInterface::OBJECTIVE_HIGHEST_PRICE:
                return $this->bidRepository->getHighestPriceBid($auction);
        }

        return null;
    }
}