<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;

interface ProductRatingInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

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
     * @return mixed
     */
    public function getCreatedDate();

    /**
     * @param mixed $createdDate
     * @return ProductInterface
     */
    public function setCreatedDate($createdDate);

    /**
     * @return AccountInterface
     */
    public function getReviewer();

    /**
     * @param AccountInterface $buyer
     * @return self
     */
    public function setReviewer($buyer);

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
     * @return mixed
     */
    public function getRateValue();

    /**
     * @param mixed $rateValue
     * @return self
     */
    public function setRateValue($rateValue);

    /**
     * @return mixed
     */
    public function getRateMessage();

    /**
     * @param mixed $rateMessage
     * @return self
     */
    public function setRateMessage($rateMessage);

    /**
     * @return BillInterface
     */
    public function getBill();

    /**
     * @param BillInterface $bill
     * @return self
     */
    public function setBill($bill);
}