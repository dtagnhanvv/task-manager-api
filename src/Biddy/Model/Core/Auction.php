<?php

namespace Biddy\Model\Core;


class Auction implements AuctionInterface
{
    protected $id;
    protected $deletedAt;
    protected $createdDate;

    protected $startDate;
    protected $endDate;
    protected $minimumPrice;
    protected $showBid;
    protected $status;
    protected $type;
    protected $objective;
    protected $incrementType;
    protected $incrementValue;
    protected $payment;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var BidInterface[]
     */
    protected $bids;

    /**
     * Auction constructor.
     */
    public function __construct()
    {
        $this->status = AuctionInterface::STATUS_BIDDING;
        $this->minimumPrice = 0;
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
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @inheritdoc
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @inheritdoc
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

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
    public function getBids()
    {
        return $this->bids;
    }

    /**
     * @inheritdoc
     */
    public function setBids($bids)
    {
        $this->bids = $bids;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMinimumPrice()
    {
        return $this->minimumPrice;
    }

    /**
     * @inheritdoc
     */
    public function setMinimumPrice($minimumPrice)
    {
        $this->minimumPrice = $minimumPrice;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isShowBid()
    {
        return $this->showBid;
    }

    /**
     * @inheritdoc
     */
    public function setShowBid($showBid)
    {
        $this->showBid = $showBid;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @inheritdoc
     */
    public function setProduct($product)
    {
        $this->product = $product;

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
    public function getObjective()
    {
        return $this->objective;
    }

    /**
     * @inheritdoc
     */
    public function setObjective($objective)
    {
        $this->objective = $objective;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIncrementType()
    {
        return $this->incrementType;
    }

    /**
     * @inheritdoc
     */
    public function setIncrementType($incrementType)
    {
        $this->incrementType = $incrementType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIncrementValue()
    {
        return $this->incrementValue;
    }

    /**
     * @inheritdoc
     */
    public function setIncrementValue($incrementValue)
    {
        $this->incrementValue = $incrementValue;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @inheritdoc
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;

        return $this;
    }
}