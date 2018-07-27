<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;

interface BidInterface extends ModelInterface
{
    const STATUS_BIDDING = 'bidding';
    const STATUS_INVALID = 'invalid';
    const STATUS_WIN = 'win';
    const STATUS_LOOSE = 'loose';
    const STATUS_CANCEL = 'cancel';
    const STATUS_REJECTED = 'rejected';

    const COUNT_STATUS = [
        BidInterface::STATUS_BIDDING,
        BidInterface::STATUS_WIN,
        BidInterface::STATUS_LOOSE
    ];

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
    public function getCategory();

    /**
     * @param mixed $category
     * @return self
     */
    public function setCategory($category);

    /**
     * @return mixed
     */
    public function getQuantity();

    /**
     * @param mixed $quantity
     * @return self
     */
    public function setQuantity($quantity);

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
     * @return AuctionInterface
     */
    public function getAuction();

    /**
     * @param AuctionInterface $auction
     * @return self
     */
    public function setAuction($auction);

    /**
     * @return BillInterface
     */
    public function getBill();

    /**
     * @param BillInterface $bill
     * @return self
     */
    public function setBill($bill);

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
     * @return mixed
     */
    public function getStatus();

    /**
     * @param mixed $status
     */
    public function setStatus($status);
}