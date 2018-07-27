<?php

namespace Biddy\Model\Core;


use Biddy\Model\User\Role\AccountInterface;

class Bill implements BillInterface
{
    protected $id;
    protected $price;
    protected $payment;
    protected $noteForSeller;
    protected $status;
    protected $deletedAt;
    protected $createdDate;

    /** @var AccountInterface */
    protected $buyer;

    /** @var AccountInterface */
    protected $seller;

    /**
     * @var BidInterface
     */
    protected $bid;

    /** @var ProductRatingInterface */
    protected $productRating;

    /**
     * Bill constructor.
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
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * @inheritdoc
     */
    public function setSeller($seller)
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBid()
    {
        return $this->bid;
    }

    /**
     * @inheritdoc
     */
    public function setBid($bid)
    {
        $this->bid = $bid;

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

    /**
     * @inheritdoc
     */
    public function getNoteForSeller()
    {
        return $this->noteForSeller;
    }

    /**
     * @inheritdoc
     */
    public function setNoteForSeller($noteForSeller)
    {
        $this->noteForSeller = $noteForSeller;

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
    public function getProductRating()
    {
        return $this->productRating;
    }

    /**
     * @inheritdoc
     */
    public function setProductRating($productRating)
    {
        $this->productRating = $productRating;

        return $this;
    }
}