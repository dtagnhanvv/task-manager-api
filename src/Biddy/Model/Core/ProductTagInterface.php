<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;

interface ProductTagInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getCreatedDate();

    /**
     * @param mixed $createdDate
     * @return self
     */
    public function setCreatedDate($createdDate);

    /**
     * @param $deletedAt
     * @return self
     */
    public function setDeletedAt($deletedAt);

    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @return TagInterface
     */
    public function getTag();

    /**
     * @param TagInterface $tag
     * @return self
     */
    public function setTag($tag);

    /**
     * @return ProductInterface
     * @return self
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     */
    public function setProduct($product);
}