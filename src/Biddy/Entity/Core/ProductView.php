<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Core\ProductView as ProductViewModel;
use Biddy\Model\User\UserEntityInterface;

class ProductView extends ProductViewModel
{
    protected $id;
    protected $createdDate;
    protected $deletedAt;

    /**
     * @var UserEntityInterface
     */
    protected $viewer;

    /** @var ProductInterface */
    protected $product;

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