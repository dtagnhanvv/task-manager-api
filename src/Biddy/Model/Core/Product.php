<?php

namespace Biddy\Model\Core;


use Biddy\Model\User\UserEntityInterface;

class Product implements ProductInterface
{
    //Basic info
    protected $id;
    protected $subject;
    protected $summary;
    protected $detail;

    //Location supporting
    /** @var  string */
    protected $address;
    protected $longitude;
    protected $latitude;

    //Biddy spec
    protected $mode;
    /** @var  string */
    protected $visibility;
    /** @var  string */
    protected $businessSetting;
    /** @var  string */
    protected $businessRule;
    /** @var  string */
    protected $commentVisibility;
    protected $deletedAt;
    protected $createdDate;
    protected $rating;
    protected $type;

    /**
     * @var UserEntityInterface
     */
    protected $seller;

    /**
     * @var CommentInterface[]
     */
    protected $comments;

    /** @var ReactionInterface[] */
    protected $reactions;

    /** @var ProductTagInterface[] */
    protected $productTags;

    /** @var ProductRatingInterface[] */
    protected $productRatings;

    /** @var AuctionInterface[] */
    protected $auctions;

    /**
     * Product constructor.
     */
    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @inheritdoc
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @inheritdoc
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @inheritdoc
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @inheritdoc
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @inheritdoc
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @inheritdoc
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBusinessSetting()
    {
        return $this->businessSetting;
    }

    /**
     * @inheritdoc
     */
    public function setBusinessSetting($businessSetting)
    {
        $this->businessSetting = $businessSetting;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBusinessRule()
    {
        return $this->businessRule;
    }

    /**
     * @inheritdoc
     */
    public function setBusinessRule($businessRule)
    {
        $this->businessRule = $businessRule;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCommentVisibility()
    {
        return $this->commentVisibility;
    }

    /**
     * @inheritdoc
     */
    public function setCommentVisibility($commentVisibility)
    {
        $this->commentVisibility = $commentVisibility;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * @inheritdoc
     */
    public function setSeller($seller)
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @inheritdoc
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @inheritdoc
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReactions()
    {
        return $this->reactions;
    }

    /**
     * @inheritdoc
     */
    public function setReactions($reactions)
    {
        $this->reactions = $reactions;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductTags()
    {
        return $this->productTags;
    }

    /**
     * @inheritdoc
     */
    public function setProductTags($productTags)
    {
        $this->productTags = $productTags;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @inheritdoc
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductRatings()
    {
        return $this->productRatings;
    }

    /**
     * @inheritdoc
     */
    public function setProductRatings($productRatings)
    {
        $this->productRatings = $productRatings;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAuctions()
    {
        return $this->auctions;
    }

    /**
     * @inheritdoc
     */
    public function setAuctions($auctions)
    {
        $this->auctions = $auctions;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return ProductInterface::TYPE_PRODUCT;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}