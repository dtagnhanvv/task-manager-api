<?php

namespace Biddy\Model\Core;


use Biddy\Model\User\UserEntityInterface;

class Comment implements CommentInterface
{
    protected $id;
    protected $createdDate;
    protected $modified;
    protected $deletedAt;
    protected $content;
    protected $contentType;
    protected $estimatedHeight;
    protected $migrationStatus;
    protected $raw;

    /**
     * @var UserEntityInterface
     */
    protected $author;

    /**
     * @var ProductInterface
     */
    protected $product;

    /** @var CommentInterface */
    protected $masterComment;

    /** @var CommentInterface[] */
    protected $childComments;

    /** @var ReactionInterface[] */
    protected $reactions;
    
    /**
     * Comment constructor.
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
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @inheritdoc
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
        
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritdoc
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @inheritdoc
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEstimatedHeight()
    {
        return $this->estimatedHeight;
    }

    /**
     * @inheritdoc
     */
    public function setEstimatedHeight($estimatedHeight)
    {
        $this->estimatedHeight = $estimatedHeight;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMigrationStatus()
    {
        return $this->migrationStatus;
    }

    /**
     * @inheritdoc
     */
    public function setMigrationStatus($migrationStatus)
    {
        $this->migrationStatus = $migrationStatus;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @inheritdoc
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @inheritdoc
     */
    public function setAuthor($author)
    {
        $this->author = $author;

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
    public function getMasterComment()
    {
        return $this->masterComment;
    }

    /**
     * @inheritdoc
     */
    public function setMasterComment($masterComment)
    {
        $this->masterComment = $masterComment;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getChildComments()
    {
        return $this->childComments;
    }

    /**
     * @inheritdoc
     */
    public function setChildComments($childComments)
    {
        $this->childComments = $childComments;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReactions()
    {
        return $this->reactions;
    }

    /**
     * @inheritdoc
     */
    public function setReactions($reactions)
    {
        $this->reactions = $reactions;

        return $this;
    }
}