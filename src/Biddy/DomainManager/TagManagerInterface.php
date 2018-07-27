<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\TagInterface;
use Biddy\Model\Core\ProductInterface;

interface TagManagerInterface extends ManagerInterface
{
    /**
     * @param ProductInterface $product
     */
    public function findTotalTagsCountByProduct(ProductInterface $product);

    /**
     * @param TagInterface $tag
     * @return mixed
     */
    public function findTotalTagsCountByTag(TagInterface $tag);
    
    /**
     * @param ProductInterface $product
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function findTagsByProduct(ProductInterface $product, $page, $limit);

    /**
     * @param TagInterface $tag
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function findTagsByTag(TagInterface $tag, $page = 1, $limit = 10);
}