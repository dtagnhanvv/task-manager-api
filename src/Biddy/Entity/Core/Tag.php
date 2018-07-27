<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\Tag as TagModel;
use Biddy\Model\Core\TagInterface;

class Tag extends TagModel
{
    protected $id;
    protected $createdDate;
    protected $deletedAt;

    protected $name;
    protected $type;
    protected $url;


    /** @var TagInterface */
    protected $parentTag;

    /** @var TagInterface[] */
    protected $childTags;

    /** @var ProductTagInterface[] */
    protected $productTags;

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