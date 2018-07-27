<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;

interface ProductViewInterface extends ModelInterface
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
     * @return AccountInterface
     */
    public function getViewer();

    /**
     * @param AccountInterface $viewer
     * @return self
     */
    public function setViewer($viewer);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     * @return self
     */
    public function setProduct($product);
    
    /**
     * @param $deletedAt
     * @return self
     */
    public function setDeletedAt($deletedAt);

    /**
     * @return mixed
     */
    public function getDeletedAt();
}