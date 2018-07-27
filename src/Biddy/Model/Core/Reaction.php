<?php

namespace Biddy\Model\Core;


use Biddy\Model\User\UserEntityInterface;

class Reaction implements ReactionInterface
{
    protected $id;
    protected $emotion;
    protected $deletedAt;
    protected $createdDate;

    /**
     * @var UserEntityInterface
     */
    protected $viewer;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var CommentInterface
     */
    protected $comment;

    /**
     * Reaction constructor.
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
    public function getEmotion()
    {
        return $this->emotion;
    }

    /**
     * @inheritdoc
     */
    public function setEmotion($emotion)
    {
        $this->emotion = $emotion;

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

    /**
     * @inheritdoc
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @inheritdoc
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }
}