<?php

namespace Biddy\Model\Core;


use Biddy\Model\User\UserEntityInterface;

class Bid implements BidInterface
{
    protected $id;
    protected $price;
    protected $category;
    protected $quantity;
    protected $deletedAt;
    protected $createdDate;
    protected $status;

    /** @var UserEntityInterface */
    protected $buyer;

    /** @var AuctionInterface */
    protected $auction;

    /** @var BillInterface */
    protected $bill;

    /**
     * Bid constructor.
     */
    public function __construct()
    {
        $this->status = BidInterface::STATUS_BIDDING;
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
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @inheritdoc
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @inheritdoc
     */
    public function setCategory($category)
    {
        $this->category = $category;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @inheritdoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    /**
     * @inheritdoc
     */
    public function setBuyer($buyer)
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAuction()
    {
        return $this->auction;
    }

    /**
     * @inheritdoc
     */
    public function setAuction($auction)
    {
        $this->auction = $auction;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBill()
    {
        return $this->bill;
    }

    /**
     * @inheritdoc
     */
    public function setBill($bill)
    {
        $this->bill = $bill;

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
}