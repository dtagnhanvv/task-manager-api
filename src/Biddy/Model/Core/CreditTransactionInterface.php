<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;

interface CreditTransactionInterface extends ModelInterface
{
    const TYPE_GUARANTEED_BID = 'GUARANTEED_BID';
    const TYPE_RETURN_GUARANTEED_BID = 'RETURN_GUARANTEED_BID';
    const TYPE_TRANSFER_CREDIT_FOR_WIN_BID = 'TRANSFER_CREDIT_FOR_WIN_BID';
    const TYPE_PAY_FEE_FIRST_TIME = 'PAY_FEE_FIRST_TIME';
    const TYPE_PAY_FEE_AT_LAST = 'PAY_FEE_AT_LAST';
    const TYPE_RETURN_FEE_AT_REJECTED = 'RETURN_FEE_AT_REJECTED';

    const TARGET_TYPE_BID = 'bid';
    const TARGET_TYPE_AUCTION = 'auction';
    const TARGET_TYPE_BILL = 'bill';
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

    /**
     * @param $deletedAt
     * @return self
     */
    public function setDeletedAt($deletedAt);

    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @return mixed
     */
    public function getCreatedDate();

    /**
     * @param mixed $createdDate
     * @return ProductInterface
     */
    public function setCreatedDate($createdDate);

    /**
     * @return mixed
     */
    public function getAmount();

    /**
     * @param mixed $amount
     * @return self
     */
    public function setAmount($amount);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param mixed $type
     * @return self
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getDetail();

    /**
     * @param mixed $detail
     * @return self
     */
    public function setDetail($detail);

    /**
     * @return mixed
     */
    public function getTargetWalletCreditBeforeTransaction();

    /**
     * @param mixed $targetWalletCreditBeforeTransaction
     * @return self
     */
    public function setTargetWalletCreditBeforeTransaction($targetWalletCreditBeforeTransaction);

    /**
     * @return mixed
     */
    public function getFromWalletCreditBeforeTransaction();

    /**
     * @param mixed $fromWalletCreditBeforeTransaction
     * @return self
     */
    public function setFromWalletCreditBeforeTransaction($fromWalletCreditBeforeTransaction);

    /**
     * @return WalletInterface
     */
    public function getFromWallet();

    /**
     * @param WalletInterface $fromWallet
     * @return self
     */
    public function setFromWallet($fromWallet);

    /**
     * @return WalletInterface
     */
    public function getTargetWallet();

    /**
     * @param WalletInterface $targetWallet
     * @return self
     */
    public function setTargetWallet($targetWallet);

    /**
     * @return mixed
     */
    public function getTargetType();

    /**
     * @param mixed $targetType
     * @return self
     */
    public function setTargetType($targetType);

    /**
     * @return mixed
     */
    public function getTargetId();

    /**
     * @param mixed $targetId
     * @return self
     */
    public function setTargetId($targetId);
}