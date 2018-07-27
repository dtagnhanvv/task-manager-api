<?php

namespace Biddy\Service\Bidding\Core;

use Biddy\DomainManager\BidManagerInterface;
use Biddy\Entity\Core\Bill;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\BillInterface;
use Doctrine\Common\Collections\Collection;

class BidStatusManager implements BidStatusManagerInterface
{
    /** @var BidManagerInterface */
    private $bidManager;

    /**
     * BidStatusManager constructor.
     * @param BidManagerInterface $bidManager
     */
    public function __construct(BidManagerInterface $bidManager)
    {
        $this->bidManager = $bidManager;
    }

    /**
     * @inheritdoc
     */
    public function createBillForWinningBids($winningBids)
    {
        $winningBids = is_array($winningBids) ? $winningBids : [$winningBids];

        foreach ($winningBids as &$winningBid) {
            if (!$winningBid instanceof BidInterface) {
                continue;
            }

            $winningBid->setStatus(BidInterface::STATUS_WIN);
            $bill = new Bill();
            $bill
                ->setStatus(BillInterface::STATUS_UNCONFIRMED)
                ->setPrice($winningBid->getPrice())
                ->setBuyer($winningBid->getBuyer())
                ->setSeller($winningBid->getAuction()->getProduct()->getSeller())
                ->setBid($winningBid)
                ->setPayment($winningBid->getAuction()->getPayment());

            $winningBid->setBill($bill);
            $this->bidManager->save($winningBid);
        }

        return $winningBids;
    }

    /**
     * @inheritdoc
     */
    public function changeStatusOfLooseBidsOnProduct(AuctionInterface $auction, $winningBids)
    {
        $bids = $auction->getBids();
        $bids = $bids instanceof Collection ? $bids->toArray() : $bids;
        $winningBids = is_array($winningBids) ? $winningBids : [$winningBids];

        if (!empty($winningBids)) {
            $winningBidIds = array_map(function ($bid) {
                if ($bid instanceof BidInterface) {
                    return $bid->getId();
                }
            }, $winningBids);
        } else {
            $winningBidIds = [];
        }

        $winningBidIds = array_filter($winningBidIds);
        $status = empty($winningBidIds) ? BidInterface::STATUS_CANCEL : BidInterface::STATUS_LOOSE;

        $looseBids = array_filter($bids, function (BidInterface $bid) use ($winningBidIds) {
            return !in_array($bid->getId(), $winningBidIds);
        });

        $bidManager = $this->bidManager;
        array_map(function (BidInterface $bid) use ($bidManager, $auction, $status) {
            $bid->setStatus($status);
            $bid->setAuction($auction);
            $bidManager->save($bid);
        }, $looseBids);
    }
}