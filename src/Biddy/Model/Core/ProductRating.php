<?php

namespace Biddy\Model\Core;


use Biddy\Model\User\UserEntityInterface;

class ProductRating implements ProductRatingInterface
{
    protected $id;
    protected $deletedAt;
    protected $createdDate;

    protected $rateValue;
    protected $rateMessage;

    /** @var UserEntityInterface */
    protected $reviewer;

    /** @var ProductInterface */
    protected $product;

    /** @var BillInterface */
    protected $bill;

    /**
     * ProductRating constructor.
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
    public function getReviewer()
    {
        return $this->reviewer;
    }

    /**
     * @inheritdoc
     */
    public function setReviewer($reviewer)
    {
        $this->reviewer = $reviewer;

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
     * @return mixed
     */
    public function getRateValue()
    {
        return $this->rateValue;
    }

    /**
     * @inheritdoc
     */
    public function setRateValue($rateValue)
    {
        $this->rateValue = $rateValue;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRateMessage()
    {
        return $this->rateMessage;
    }

    /**
     * @inheritdoc
     */
    public function setRateMessage($rateMessage)
    {
        $this->rateMessage = $rateMessage;

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
}