<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;

interface BillInterface extends ModelInterface
{
    const STATUS_UNCONFIRMED = 'unconfirmed';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_REJECTED = 'rejected';
    const SUPPORT_STATUS = [
        self::STATUS_UNCONFIRMED,
        self::STATUS_CONFIRMED,
        self::STATUS_REJECTED,
    ];

    const GROUP_NEED_CONFIRMED = 'needConfirmed';
    const GROUP_WAIT_CONFIRMED = 'waitConfirmed';

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
     * @return mixed
     */
    public function getPrice();

    /**
     * @param mixed $price
     * @return self
     */
    public function setPrice($price);

    /**
     * @return mixed
     */
    public function getPayment();

    /**
     * @param mixed $payment
     * @return self
     */
    public function setPayment($payment);

    /**
     * @return mixed
     */
    public function getNoteForSeller();

    /**
     * @param mixed $noteForSeller
     * @return self
     */
    public function setNoteForSeller($noteForSeller);

    /**
     * @return AccountInterface
     */
    public function getBuyer();

    /**
     * @param AccountInterface $buyer
     * @return self
     */
    public function setBuyer($buyer);

    /**
     * @return AccountInterface
     */
    public function getSeller();

    /**
     * @param AccountInterface $seller
     * @return self
     */
    public function setSeller($seller);

    /**
     * @return BidInterface
     */
    public function getBid();

    /**
     * @param BidInterface $bid
     * @return self
     */
    public function setBid($bid);

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @param mixed $status
     * @return self
     */
    public function setStatus($status);

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
     * @return ProductRatingInterface
     */
    public function getProductRating();

    /**
     * @param ProductRatingInterface $productRating
     * @return self
     */
    public function setProductRating($productRating);
}