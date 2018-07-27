<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\Bid as BidModel;
use Biddy\Model\Core\BillInterface;
use Biddy\Model\User\UserEntityInterface;

class Bid extends BidModel
{
    protected $id;
    protected $price;
    protected $category;
    protected $quantity;
    protected $deletedAt;
    protected $createdDate;
    protected $status;

    /** @var UserEntityInterface */
    protected $buyer;

    /** @var AuctionInterface */
    protected $auction;

    /** @var BillInterface */
    protected $bill;

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