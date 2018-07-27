<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\Task as TaskModel;
use Biddy\Model\User\UserEntityInterface;

class Task extends TaskModel
{
    protected $id;
    protected $deletedAt;
    protected $createdDate;

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
     * @inheritdoc
     *
     * inherit constructor for inheriting all default initialized value
     */
    public function __construct()
    {
        parent::__construct();
    }
}