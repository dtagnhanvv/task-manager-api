<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ProductRatingInterface;
use Biddy\Model\Core\ProductTagInterface;
use Biddy\Model\Core\ReactionInterface;
use Biddy\Model\Core\Product as ProductModel;
use Biddy\Model\User\UserEntityInterface;

class Product extends ProductModel
{
    protected $id;
    protected $subject;
    protected $summary;
    protected $detail;
    protected $address;
    protected $longitude;
    protected $latitude;
    protected $mode;
    protected $visibility;
    protected $businessSetting;
    protected $businessRule;
    protected $commentVisibility;
    protected $deletedAt;
    protected $createdDate;
    protected $rating;
    protected $type;

    /**
     * @var UserEntityInterface
     */
    protected $seller;

    /**
     * @var CommentInterface[]
     */
    protected $comments;

    /** @var ReactionInterface[] */
    protected $reactions;

    /** @var ProductTagInterface[] */
    protected $productTags;

    /** @var ProductRatingInterface[] */
    protected $productRatings;

    /** @var AuctionInterface[] */
    protected $auctions;

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