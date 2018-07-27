<?php

namespace Biddy\Model\Core;


use Biddy\Model\User\UserEntityInterface;

class ProductView implements ProductViewInterface
{
    protected $id;
    protected $createdDate;
    protected $deletedAt;

    /**
     * @var UserEntityInterface
     */
    protected $viewer;

    /** @var ProductInterface */
    protected $product;

    /**
     * ProductView constructor.
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
     * @return mixed
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
    public function getViewer()
    {
        return $this->viewer;
    }

    /**
     * @inheritdoc
     */
    public function setViewer($viewer)
    {
        $this->viewer = $viewer;

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
}