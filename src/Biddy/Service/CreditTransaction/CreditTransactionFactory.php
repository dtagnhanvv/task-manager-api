<?php

namespace Biddy\Service\CreditTransaction;

use Biddy\Entity\Core\CreditTransaction;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\BillInterface;
use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\Core\WalletInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Service\Util\AuctionUtilTrait;
use Biddy\Worker\Manager;

class CreditTransactionFactory implements CreditTransactionFactoryInterface
{
    use AuctionUtilTrait;

    /** @var Manager */
    private $manager;
    private $feeCredit;
    private $feeOffline;

    /**
     * CreditTransactionFactory constructor.
     * @param Manager $manager
     * @param $feeCredit
     * @param $feeOffline
     */
    public function __construct(Manager $manager, $feeCredit, $feeOffline)
    {
        $this->manager = $manager;
        $this->feeCredit = $feeCredit;
        $this->feeOffline = $feeOffline;
    }

    /**
     * @inheritdoc
     */
    public function transferCreditTransaction(BillInterface $bill)
    {
        $bid = $bill->getBid();

        if (!$bid instanceof BidInterface || $bid->getStatus() != BidInterface::STATUS_WIN) {
            return null;
        }

        $auction = $bid->getAuction();
        $product = $auction->getProduct();

        $fromWallet = $bid->getBuyer()->getInsureWallet();

        if ($auction->getPayment() == AuctionInterface::PAYMENT_OFFLINE) {
            $targetWallet = $bid->getBuyer()->getBasicWallet();
            $this->validateWallet($bid->getBuyer(), $targetWallet);
        } else {
            $targetWallet = $product->getSeller()->getBasicWallet();
            $this->validateWallet($product->getSeller(), $targetWallet);
        }

        $creditTransaction = new CreditTransaction();
        $creditTransaction
            ->setAmount($bid->getPrice())
            ->setFromWallet($fromWallet)
            ->setTargetWallet($targetWallet)
            ->setType(CreditTransactionInterface::TYPE_TRANSFER_CREDIT_FOR_WIN_BID)
            ->setTargetType(CreditTransactionInterface::TARGET_TYPE_BILL)
            ->setTargetId($bill->getId())
            ->setDetail(sprintf("Hoàn thành giao dịch cho sản phẩm %s (ID: %s)", $product->getSubject(), $product->getId()));

        return $creditTransaction;
    }

    /**
     * @param AuctionInterface $auction
     * @return mixed
     */
    public function payFeeToNewAuction(AuctionInterface $auction)
    {
        $product = $auction->getProduct();
        $seller = $product->getSeller();;

        /** @var WalletInterface $fromWallet */
        $fromWallet = $seller->getBasicWallet();
        $this->validateWallet($seller, $fromWallet);

        if (empty($fromWallet->getCredit()) || $fromWallet->getCredit() <= 0) {
            return null;
        }
        
        /** @var WalletInterface $targetWallet */
        $targetWallet = $seller->getFeeWallet();
        $this->validateWallet($seller, $targetWallet);

        $rate = $this->getFeeRateForAuction($auction, $this->feeCredit, $this->feeOffline);
        $fee = $auction->getMinimumPrice() * $rate;

        $creditTransaction = new CreditTransaction();
        $creditTransaction
            ->setAmount($fee)
            ->setFromWallet($fromWallet)
            ->setTargetWallet($targetWallet)
            ->setType(CreditTransactionInterface::TYPE_PAY_FEE_FIRST_TIME)
            ->setTargetType(CreditTransactionInterface::TARGET_TYPE_AUCTION)
            ->setTargetId($auction->getId())
            ->setDetail(sprintf("Thu tạm %s credit tiền phí, (tỉ lệ %s%s) cho đấu giá sản phẩm %s: (ID: %s)", $fee, $rate * 100, '%', $product->getSubject(), $product->getId()));

        return $creditTransaction;
    }

    /**
     * @inheritdoc
     */
    public function payFeeToFeeWallet(BillInterface $bill)
    {
        $bid = $bill->getBid();
        $auction = $bid->getAuction();
        $rate = $this->getFeeRateForAuction($auction, $this->feeCredit, $this->feeOffline);

        $originalFee = $auction->getMinimumPrice() * $rate;
        $finalFee = $bid->getPrice() * $rate;
        $fee = $finalFee - $originalFee;

        if (empty($fee)) {
            return null;
        }

        $product = $auction->getProduct();
        $fromUser = $product->getSeller();

        /** @var WalletInterface $fromWallet */
        $fromWallet = $fromUser->getBasicWallet();
        $this->validateWallet($fromUser, $fromWallet);
        /** @var WalletInterface $targetWallet */
        $targetWallet = $fromUser->getFeeWallet();
        $this->validateWallet($fromUser, $targetWallet);

        $creditTransaction = new CreditTransaction();
        $creditTransaction
            ->setAmount($fee)
            ->setFromWallet($fromWallet)
            ->setTargetWallet($targetWallet)
            ->setType(CreditTransactionInterface::TYPE_PAY_FEE_AT_LAST)
            ->setTargetType(CreditTransactionInterface::TARGET_TYPE_BILL)
            ->setTargetId($bill->getId())
            ->setDetail(sprintf("Tất toán %s tiền phí (tỉ lệ %s%s)  cho giao dịch %s, sản phẩm %s (ID: %s)", $fee, $rate * 100, '%', $bill->getId(), $product->getSubject(), $product->getId()));

        return $creditTransaction;
    }

    /**
     * @inheritdoc
     */
    public function returnFeeToBasicWallet(BillInterface $bill)
    {
        if ($bill->getStatus() != BillInterface::STATUS_REJECTED) {
            return null;
        }

        $bid = $bill->getBid();
        $auction = $bid->getAuction();
        $rate = $this->getFeeRateForAuction($auction, $this->feeCredit, $this->feeOffline);

        $originalFee = $auction->getMinimumPrice() * $rate;
        $finalFee = $bid->getPrice() * $rate;
        $fee = $finalFee - $originalFee;

        if (empty($fee)) {
            return null;
        }

        $product = $auction->getProduct();
        $fromUser = $product->getSeller();

        /** @var WalletInterface $fromWallet */
        $fromWallet = $fromUser->getFeeWallet();
        $this->validateWallet($fromUser, $fromWallet);
        /** @var WalletInterface $targetWallet */
        $targetWallet = $fromUser->getBasicWallet();
        $this->validateWallet($fromUser, $targetWallet);

        $creditTransaction = new CreditTransaction();
        $creditTransaction
            ->setAmount($fee)
            ->setFromWallet($fromWallet)
            ->setTargetWallet($targetWallet)
            ->setType(CreditTransactionInterface::TYPE_RETURN_FEE_AT_REJECTED)
            ->setTargetType(CreditTransactionInterface::TARGET_TYPE_BILL)
            ->setTargetId($bill->getId())
            ->setDetail(sprintf("Trả lại %s tiền phí vì giao dịch %s bị từ chối, sản phẩm %s (ID: %s)", $fee, $bill->getId(), $product->getSubject(), $product->getId()));

        return $creditTransaction;
    }

    /**
     * @param UserRoleInterface $user
     * @param $wallet
     */
    public function validateWallet(UserRoleInterface $user, $wallet)
    {
        if ($wallet instanceof WalletInterface) {
            return;
        }

        $this->manager->createWalletsForUser($user->getId(), CreditTransactionFactory::class);
    }
}