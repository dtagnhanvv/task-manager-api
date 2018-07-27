<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\Comment as CommentModel;
use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ReactionInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\UserEntityInterface;

class Comment extends CommentModel
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
     * @inheritdoc
     *
     * inherit constructor for inheriting all default initialized value
     */
    public function __construct()
    {
        parent::__construct();
    }
}