<?php

namespace Biddy\Service\CreditTransaction;


use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\BillInterface;

interface CreditTransactionFactoryInterface
{
    /**
     * @param BillInterface $bill
     * @return mixed
     */
    public function transferCreditTransaction(BillInterface $bill);

    /**
     * @param BillInterface $bill
     * @return mixed
     */
    public function payFeeToFeeWallet(BillInterface $bill);

    /**
     * @param BillInterface $bill
     * @return mixed
     */
    public function returnFeeToBasicWallet(BillInterface $bill);

    /**
     * @param AuctionInterface $auction
     * @return mixed
     */
    public function payFeeToNewAuction(AuctionInterface $auction);
}