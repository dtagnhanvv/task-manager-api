<?php

namespace Biddy\Model\Core;


use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Model\User\UserEntityInterface;

class Alert implements AlertInterface
{
    public static $SUPPORTED_ALERT_CODES = [
    ];

    public static $ALERT_CODE_TO_TYPE_MAP = [
    ];

    protected $id;
    protected $code;
    protected $isRead;
    protected $detail;
    protected $createdDate;
    protected $type;
    protected $deletedAt;
    /** @var boolean */
    protected $isSent;

    protected $targetType;
    protected $targetId;

    /**
     * @var UserEntityInterface
     */
    protected $account;

    public function __construct()
    {
        $this->isRead = false;
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
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * @inheritdoc
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
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
    }

    /**
     * @inheritdoc
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @inheritdoc
     */
    public function setAccount($account)
    {
        $this->account = $account;
        
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsSent()
    {
        return $this->isSent;
    }

    /**
     * @inheritdoc
     */
    public function setIsSent($isSent)
    {
        $this->isSent = $isSent;

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
    public function getTargetType()
    {
        return $this->targetType;
    }

    /**
     * @inheritdoc
     */
    public function setTargetType($targetType)
    {
        $this->targetType = $targetType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * @inheritdoc
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;

        return $this;
    }
}