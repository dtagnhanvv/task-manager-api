<?php

namespace Biddy\Service\Bidding\Core;


use Biddy\Model\Core\AuctionInterface;
use Biddy\Service\Util\PublicSimpleException;

interface AuctionCloserInterface
{
    /**
     * @param AuctionInterface $auction
     * @param string $action
     * @return AuctionRuleInterface
     * @throws PublicSimpleException
     */
    public function getAuctionRuleForProduct(AuctionInterface $auction, $action = AuctionRuleInterface::AUTOMATED);

    /**
     * @param AuctionInterface $auction
     * @param array $params
     * @return mixed
     * @throws PublicSimpleException
     */
    public function closeAuction(AuctionInterface $auction, $params = []);
}