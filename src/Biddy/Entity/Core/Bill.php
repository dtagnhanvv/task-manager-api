<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\Bill as BillModel;
use Biddy\Model\Core\ProductRatingInterface;
use Biddy\Model\User\UserEntityInterface;

class Bill extends BillModel
{
    protected $id;
    protected $price;
    protected $payment;
    protected $noteForSeller;
    protected $status;
    protected $deletedAt;
    protected $createdDate;

    /** @var UserEntityInterface */
    protected $buyer;

    /** @var UserEntityInterface */
    protected $seller;

    /**
     * @var BidInterface
     */
    protected $bid;

    /** @var ProductRatingInterface */
    protected $productRating;
    
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