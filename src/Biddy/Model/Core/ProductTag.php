<?php

namespace Biddy\Model\Core;


class ProductTag implements ProductTagInterface
{
    protected $id;
    protected $createdDate;
    protected $deletedAt;

    /** @var TagInterface */
    protected $tag;

    /** @var ProductInterface */
    protected $product;

    /**
     * ProductTag constructor.
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
     * @return TagInterface
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @inheritdoc
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

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