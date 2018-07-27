<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\Reaction as ReactionModel;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\UserEntityInterface;

class Reaction extends ReactionModel
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
     * @inheritdoc
     *
     * inherit constructor for inheriting all default initialized value
     */
    public function __construct()
    {
        parent::__construct();
    }
}