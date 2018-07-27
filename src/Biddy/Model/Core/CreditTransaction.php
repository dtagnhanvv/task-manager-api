<?php

namespace Biddy\Model\Core;


class CreditTransaction implements CreditTransactionInterface
{
    protected $id;
    protected $deletedAt;
    protected $createdDate;

    protected $amount;
    protected $type;
    protected $detail;
    protected $targetWalletCreditBeforeTransaction;
    protected $fromWalletCreditBeforeTransaction;

    protected $targetType;
    protected $targetId;

    /** @var WalletInterface */
    protected $fromWallet;

    /** @var WalletInterface */
    protected $targetWallet;

    /**
     * CreditTransaction constructor.
     */
    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @inheritdoc
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @inheritdoc
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @inheritdoc
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTargetWalletCreditBeforeTransaction()
    {
        return $this->targetWalletCreditBeforeTransaction;
    }

    /**
     * @inheritdoc
     */
    public function setTargetWalletCreditBeforeTransaction($targetWalletCreditBeforeTransaction)
    {
        $this->targetWalletCreditBeforeTransaction = $targetWalletCreditBeforeTransaction;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFromWalletCreditBeforeTransaction()
    {
        return $this->fromWalletCreditBeforeTransaction;
    }

    /**
     * @inheritdoc
     */
    public function setFromWalletCreditBeforeTransaction($fromWalletCreditBeforeTransaction)
    {
        $this->fromWalletCreditBeforeTransaction = $fromWalletCreditBeforeTransaction;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFromWallet()
    {
        return $this->fromWallet;
    }

    /**
     * @inheritdoc
     */
    public function setFromWallet($fromWallet)
    {
        $this->fromWallet = $fromWallet;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTargetWallet()
    {
        return $this->targetWallet;
    }

    /**
     * @inheritdoc
     */
    public function setTargetWallet($targetWallet)
    {
        $this->targetWallet = $targetWallet;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTargetType()
    {
        return $this->targetType;
    }

    /**
     * @inheritdoc
     */
    public function setTargetType($targetType)
    {
        $this->targetType = $targetType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * @inheritdoc
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;

        return $this;
    }
}