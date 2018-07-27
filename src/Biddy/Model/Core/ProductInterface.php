<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;

interface ProductInterface extends ModelInterface
{
    const MODE_DRAFT = 'draft';
    const MODE_PUBLISHED = 'published';
    const SUPPORT_MODES = [
        self::MODE_DRAFT,
        self::MODE_PUBLISHED
    ];

    const BUSINESS_RULE_BIDDING = 'bidding';
    const BUSINESS_RULE_NO_BIDDING = 'no_bidding';
    const SUPPORT_BUSINESS_RULES = [
        self::BUSINESS_RULE_BIDDING,
        self::BUSINESS_RULE_NO_BIDDING,
    ];

    const BUSINESS_SETTINGS_BUY = 'buy';
    const BUSINESS_SETTINGS_SELL = 'sell';
    const BUSINESS_SETTINGS_RENT = 'rent';
    const BUSINESS_SETTINGS_LEASE = 'lease';
    const SUPPORT_BUSINESS_SETTINGS = [
        self::BUSINESS_SETTINGS_BUY,
        self::BUSINESS_SETTINGS_SELL,
        self::BUSINESS_SETTINGS_RENT,
        self::BUSINESS_SETTINGS_LEASE,
    ];

    const VISIBILITY_PRIVATE = 'private';
    const VISIBILITY_EVERY_ONE = 'everyone';
    const VISIBILITY_CUSTOM_PRIVATE = 'customPrivate';
    const SUPPORT_COMMENT_VISIBILITIES = [
        self::VISIBILITY_CUSTOM_PRIVATE,
        self::VISIBILITY_EVERY_ONE,
        self::VISIBILITY_PRIVATE,
    ];

    const TYPE_PRODUCT = 'product';
    const TYPE_FREELANCER = 'freelancer';
    const TYPE_PROFESSIONAL = 'professional';

    /**
     * @return mixed
     */
    public function getSubject();

    /**
     * @param mixed $subject
     * @return ProductInterface
     */
    public function setSubject($subject);

    /**
     * @return mixed
     */
    public function getSummary();

    /**
     * @param mixed $summary
     * @return ProductInterface
     */
    public function setSummary($summary);

    /**
     * @return mixed
     */
    public function getDetail();

    /**
     * @param mixed $detail
     * @return ProductInterface
     */
    public function setDetail($detail);

    /**
     * @return string
     */
    public function getAddress();

    /**
     * @param string $address
     * @return ProductInterface
     */
    public function setAddress($address);

    /**
     * @return array
     */
    public function getLongitude();

    /**
     * @param $longitude
     * @return ProductInterface
     */
    public function setLongitude($longitude);

    /**
     * @return mixed
     */
    public function getLatitude();

    /**
     * @param mixed $latitude
     * @return self
     */
    public function setLatitude($latitude);

    /**
     * @return mixed
     */
    public function getMode();

    /**
     * @param mixed $mode
     * @return ProductInterface
     */
    public function setMode($mode);

    /**
     * @return string
     */
    public function getVisibility();

    /**
     * @param string $visibility
     * @return ProductInterface
     */
    public function setVisibility($visibility);

    /**
     * @return string
     */
    public function getBusinessSetting();

    /**
     * @param string $businessSetting
     * @return ProductInterface
     */
    public function setBusinessSetting($businessSetting);

    /**
     * @return string
     */
    public function getBusinessRule();

    /**
     * @param string $businessRule
     * @return ProductInterface
     */
    public function setBusinessRule($businessRule);

    /**
     * @return string
     */
    public function getCommentVisibility();

    /**
     * @param string $commentVisibility
     * @return ProductInterface
     */
    public function setCommentVisibility($commentVisibility);

    /**
     * @return AccountInterface
     */
    public function getSeller();

    /**
     * @param AccountInterface $seller
     * @return ProductInterface
     */
    public function setSeller($seller);

    /**
     * @param $deletedAt
     * @return ProductInterface
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
     * @return CommentInterface[]
     */
    public function getComments();

    /**
     * @param CommentInterface[] $comments
     * @return self
     */
    public function setComments($comments);

    /**
     * @return ReactionInterface[]
     */
    public function getReactions();

    /**
     * @param ReactionInterface[] $reactions
     * @return self
     */
    public function setReactions($reactions);

    /**
     * @return ProductTagInterface[]
     */
    public function getProductTags();

    /**
     * @param ProductTagInterface[] $productTags
     * @return self
     */
    public function setProductTags($productTags);

    /**
     * @return mixed
     */
    public function getRating();

    /**
     * @param mixed $rating
     * @return self
     */
    public function setRating($rating);

    /**
     * @return ProductRatingInterface[]
     */
    public function getProductRatings();

    /**
     * @param ProductRatingInterface[] $productRatings
     */
    public function setProductRatings($productRatings);

    /**
     * @return AuctionInterface[]
     */
    public function getAuctions();

    /**
     * @param AuctionInterface[] $auctions
     * @return self
     */
    public function setAuctions($auctions);

    /**
     * @return mixed
     */
    public function getType();
    
    /**
     * @param mixed $type
     * @return self
     */
    public function setType($type);
}