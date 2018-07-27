<?php

namespace Biddy\Model\Core;


use Biddy\Model\User\Role\UserRoleInterface;

class Wallet implements WalletInterface
{
    protected $id;
    protected $createdDate;
    protected $deletedAt;

    protected $type;
    protected $name;
    protected $currency;
    protected $validFrom;
    protected $expiredAt;
    protected $credit;
    protected $previousCredit;

    /**
     * @var UserRoleInterface
     */
    protected $owner;

    /** @var CreditTransactionInterface[] */
    protected $creditTransactions;

    /**
     * Wallet constructor.
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
    public function getDeletedAt()
    {
        return $this->deletedAt;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @inheritdoc
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * @inheritdoc
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * @inheritdoc
     */
    public function setExpiredAt($expiredAt)
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @inheritdoc
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPreviousCredit()
    {
        return $this->previousCredit;
    }

    /**
     * @inheritdoc
     */
    public function setPreviousCredit($previousCredit)
    {
        $this->previousCredit = $previousCredit;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @inheritdoc
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreditTransactions()
    {
        return $this->creditTransactions;
    }

    /**
     * @inheritdoc
     */
    public function setCreditTransactions($creditTransactions)
    {
        $this->creditTransactions = $creditTransactions;

        return $this;
    }
}