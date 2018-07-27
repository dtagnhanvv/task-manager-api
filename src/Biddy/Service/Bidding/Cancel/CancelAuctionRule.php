<?php

namespace Biddy\Service\Bidding\Cancel;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Repository\Core\BidRepositoryInterface;
use Biddy\Service\Bidding\Core\AuctionRuleInterface;

class CancelAuctionRule implements AuctionRuleInterface
{
    //Configurable list
    private $supportActions;

    /**
     * AutomatedAuctionRule constructor.
     * @param $supportRules
     */
    public function __construct($supportRules)
    {
        $this->supportActions = $supportRules;
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
        if ($auction->getType() != AuctionRuleInterface::CANCEL) {
            return null;
        }

        return null;
    }
}