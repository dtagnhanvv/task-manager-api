<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\BillInterface;
use Biddy\Model\Core\ProductRating as ProductRatingModel;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\UserEntityInterface;

class ProductRating extends ProductRatingModel
{
    protected $id;
    protected $deletedAt;
    protected $createdDate;

    protected $rateValue;
    protected $rateMessage;

    /** @var UserEntityInterface */
    protected $reviewer;

    /** @var ProductInterface */
    protected $product;

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