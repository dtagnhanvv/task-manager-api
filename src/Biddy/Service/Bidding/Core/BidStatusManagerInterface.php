<?php

namespace Biddy\Service\Bidding\Core;


use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;

interface BidStatusManagerInterface
{
    /**
     * @param $bid
     * @return BidInterface
     */
    public function createBillForWinningBids($bid);

    /**
     * @param AuctionInterface $auction
     * @param $winningBids
     */
    public function changeStatusOfLooseBidsOnProduct(AuctionInterface $auction, $winningBids);
}