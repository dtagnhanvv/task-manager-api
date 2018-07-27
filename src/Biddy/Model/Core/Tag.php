<?php

namespace Biddy\Model\Core;


class Tag implements TagInterface
{
    protected $id;
    protected $createdDate;
    protected $deletedAt;

    protected $name;
    protected $type;
    protected $url;


    /** @var TagInterface */
    protected $parentTag;

    /** @var TagInterface[] */
    protected $childTags;

    /** @var ProductTagInterface[] */
    protected $productTags;

    /**
     * Tag constructor.
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
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParentTag()
    {
        return $this->parentTag;
    }

    /**
     * @inheritdoc
     */
    public function setParentTag($parentTag)
    {
        $this->parentTag = $parentTag;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getChildTags()
    {
        return $this->childTags;
    }

    /**
     * @inheritdoc
     */
    public function setChildTags($childTags)
    {
        $this->childTags = $childTags;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductTags()
    {
        return $this->productTags;
    }

    /**
     * @inheritdoc
     */
    public function setProductTags($productTags)
    {
        $this->productTags = $productTags;

        return $this;
    }
}