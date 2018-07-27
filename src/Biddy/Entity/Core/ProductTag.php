<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Core\ProductTag as ProductTagModel;
use Biddy\Model\Core\TagInterface;

class ProductTag extends ProductTagModel
{
    protected $id;
    protected $createdDate;
    protected $deletedAt;

    /** @var TagInterface */
    protected $tag;

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