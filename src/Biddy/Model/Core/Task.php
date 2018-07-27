<?php

namespace Biddy\Model\Core;


use Biddy\Model\User\UserEntityInterface;

class Task implements TaskInterface
{
    protected $id;
    protected $deletedAt;
    protected $createdDate;

    protected $project;
    protected $cardNumber;
    protected $releasePlan;
    protected $board;
    protected $status;
    protected $review;

    /** @var UserEntityInterface */
    protected $owner;

    /** @var UserEntityInterface */
    protected $reviewer;

    /**
     * Task constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
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
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @inheritdoc
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReleasePlan()
    {
        return $this->releasePlan;
    }

    /**
     * @inheritdoc
     */
    public function setReleasePlan($releasePlan)
    {
        $this->releasePlan = $releasePlan;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @inheritdoc
     */
    public function setBoard($board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * @return mixed
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
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @inheritdoc
     */
    public function setReview($review)
    {
        $this->review = $review;

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
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @inheritdoc
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }
}