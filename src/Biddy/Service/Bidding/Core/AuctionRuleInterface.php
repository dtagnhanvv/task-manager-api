<?php

namespace Biddy\Service\Bidding\Core;


use Biddy\Model\Core\AuctionInterface;

interface AuctionRuleInterface
{
    const AUTOMATED = 'automated';
    const MANUAL = 'manual';
    const CANCEL = 'cancel';

    /**
     * @param AuctionInterface $auction
     * @param string $action
     * @return bool
     */
    public function supportProduct(AuctionInterface $auction, $action = AuctionRuleInterface::AUTOMATED);

    /**
     * @param AuctionInterface $auction
     * @param array $params
     * @return mixed
     */
    public function getWinningBids(AuctionInterface $auction, $params = []);
}