<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;

interface AuctionInterface extends ModelInterface
{
    const STATUS_BIDDING = 'bidding';
    const STATUS_CLOSED = 'closed';

    const PAYMENT_CREDIT = 'credit';
    const PAYMENT_OFFLINE = 'offline';

    const OBJECTIVE_LOWEST_PRICE = 'lowest_price';
    const OBJECTIVE_HIGHEST_PRICE = 'highest_price';

    const INCREMENT_TYPE_CREDIT = 'credit';
    const INCREMENT_TYPE_PERCENT = 'percent';
    const INCREMENT_TYPE_GREATER = 'greater';

    const SUPPORT_PAYMENTS = [
        self::PAYMENT_CREDIT,
        self::PAYMENT_OFFLINE
    ];

    /**
     * @return string
     */
    public function getStartDate();

    /**
     * @param $startDate
     * @return AuctionInterface
     */
    public function setStartDate($startDate);

    /**
     * @return mixed
     */
    public function getEndDate();

    /**
     * @param mixed $endDate
     * @return self
     */
    public function setEndDate($endDate);

    /**
     * @param $deletedAt
     * @return AuctionInterface
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
     * @return AuctionInterface
     */
    public function setCreatedDate($createdDate);

    /**
     * @return BidInterface[]
     */
    public function getBids();

    /**
     * @param BidInterface[] $bids
     * @return self
     */
    public function setBids($bids);

    /**
     * @return mixed
     */
    public function getMinimumPrice();

    /**
     * @param mixed $minimumPrice
     * @return self
     */
    public function setMinimumPrice($minimumPrice);

    /**
     * @return boolean
     */
    public function isShowBid();

    /**
     * @param boolean $showBid
     * @return self
     */
    public function setShowBid($showBid);

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
    public function getType();
    /**
     * @param mixed $type
     * @return self
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getObjective();

    /**
     * @param mixed $objective
     * @return self
     */
    public function setObjective($objective);

    /**
     * @return mixed
     */
    public function getIncrementType();

    /**
     * @param mixed $incrementType
     * @return self
     */
    public function setIncrementType($incrementType);

    /**
     * @return mixed
     */
    public function getIncrementValue();

    /**
     * @param mixed $incrementValue
     * @return self
     */
    public function setIncrementValue($incrementValue);

    /**
     * @return mixed
     */
    public function getPayment();

    /**
     * @param mixed $payment
     * @return self
     */
    public function setPayment($payment);
}