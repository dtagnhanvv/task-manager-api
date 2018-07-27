<?php

namespace Biddy\Service\Bidding\Manual;

use Biddy\DomainManager\BidManagerInterface;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Service\Bidding\Core\AuctionRuleInterface;
use Biddy\Service\Util\PublicSimpleException;

class ManualAuctionRule implements AuctionRuleInterface
{
    //Configurable list
    private $supportActions;

    /** @var BidManagerInterface */
    private $bidManager;

    /**
     * ManualAuctionRule constructor.
     * @param $supportRule
     * @param BidManagerInterface $bidManager
     */
    public function __construct($supportRule, BidManagerInterface $bidManager)
    {
        $this->supportActions = $supportRule;
        $this->bidManager = $bidManager;
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
        $bid = $this->getBidFromParams($params);
        if ($bid->getAuction()->getId() != $auction->getId()) {
            throw new PublicSimpleException(sprintf('Bid %s is not related to product %s', $bid->getId(), $auction->getId()));
        }

        return $bid;
    }

    /**
     * @param $params
     * @return BidInterface
     * @throws PublicSimpleException
     */
    private function getBidFromParams($params)
    {
        if (!isset($params['bidId'])) {
            throw new PublicSimpleException('Could not find bid id');
        }
        $bid = $this->bidManager->find($params['bidId']);
        if (!$bid instanceof BidInterface) {
            throw new PublicSimpleException('Could not find bid id');
        }

        return $bid;
    }
}