<?php

namespace Biddy\Service\Bidding\Core;


use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BillInterface;

interface BidCreditManagerInterface
{
    /**
     * @param BillInterface $bill
     * @return
     */
    public function transferCreditTransaction(BillInterface $bill);

    /**
     * @param AuctionInterface $auction
     * @return mixed
     */
    public function payFeeForNewAuction(AuctionInterface $auction);
    
    /**
     * @param BillInterface $bill
     * @return mixed
     */
    public function payFeeForBill(BillInterface $bill);

    /**
     * @param BillInterface $bill
     * @return mixed
     */
    public function returnFeeForRejectedBill(BillInterface $bill);
}