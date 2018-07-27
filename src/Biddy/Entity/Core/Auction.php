<?php

namespace Biddy\Entity\Core;

use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Core\Auction as AuctionModel;
use Biddy\Model\Core\BidInterface;

class Auction extends AuctionModel
{
    protected $id;
    protected $deletedAt;
    protected $createdDate;

    protected $startDate;
    protected $endDate;
    protected $minimumPrice;
    protected $showBid;
    protected $status;
    protected $type;
    protected $objective;
    protected $incrementType;
    protected $incrementValue;
    protected $payment;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var BidInterface[]
     */
    protected $bids;

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