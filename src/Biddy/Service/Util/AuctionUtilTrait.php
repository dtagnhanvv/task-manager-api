<?php


namespace Biddy\Service\Util;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Repository\Core\BidRepositoryInterface;

trait AuctionUtilTrait
{
    /**
     * @param $auctions
     * @param UserRoleInterface $user
     * @param BidRepositoryInterface $bidRepository
     * @return array
     */
    function serializeAuctions($auctions, UserRoleInterface $user, BidRepositoryInterface $bidRepository)
    {
        $groups = [];
        foreach ($auctions as $auction) {
            if (!$auction instanceof AuctionInterface) {
                continue;
            }
            $group = [];
            $group['id'] = $auction->getId();
            $group['type'] = $auction->getType();
            $group['startDate'] = $auction->getStartDate();
            $group['endDate'] = $auction->getEndDate();
            $group['status'] = $auction->getStatus();

            $product = $auction->getProduct();
            $group['product'] = $product->getId();
            $group['subject'] = $product->getSubject();

            if ($product->getSeller() instanceof UserRoleInterface) {
                $group['seller'] = $this->serializeSingleUser($product->getSeller());
            }

            $group['bids']['totalBids'] = $bidRepository->getTotalBidsForAuction($auction);
            $group['bids']['totalBuyers'] = $bidRepository->getTotalBuyersForAuction($auction);
            $group['bids']['highestPrice'] = $bidRepository->getHighestPriceForAuction($auction);
            $group['bids']['lowestPrice'] = $bidRepository->getLowestPriceForAuction($auction);
            $group['bids']['bidStatus'] = $this->getBidStatus($auction, $bidRepository, $user);

            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * @param AuctionInterface $auction
     * @param BidRepositoryInterface $bidRepository
     * @return int
     */
    public function calculateNextPrice(AuctionInterface $auction, BidRepositoryInterface $bidRepository)
    {
        $highestPrice = $bidRepository->getHighestPriceForAuction($auction);

        $incrementValue = $auction->getIncrementValue();
        if ($highestPrice < $auction->getMinimumPrice()) {
            return $auction->getMinimumPrice();
        }

        $targetPrice = 0;

        switch ($auction->getIncrementType()) {
            case AuctionInterface::INCREMENT_TYPE_CREDIT:
                $targetPrice = $highestPrice + $incrementValue;
                break;
            case AuctionInterface::INCREMENT_TYPE_PERCENT:
                $targetPrice = $highestPrice * (1 + $incrementValue / 100);
                break;
            case AuctionInterface::INCREMENT_TYPE_GREATER:
                $targetPrice = $highestPrice;
                break;
            default:
        }

        return $targetPrice;
    }

    /**
     * @param AuctionInterface $auction
     * @param $feeCredit
     * @param $feeOffline
     * @return int
     */
    public function getFeeRateForAuction(AuctionInterface $auction, $feeCredit, $feeOffline)
    {
        if ($auction->getPayment() == AuctionInterface::PAYMENT_CREDIT) {
            return $feeCredit;
        }

        if ($auction->getPayment() == AuctionInterface::PAYMENT_OFFLINE) {
            return $feeOffline;
        }

        return 0;
    }
}