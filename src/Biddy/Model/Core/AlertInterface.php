<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\UserRoleInterface;

interface AlertInterface extends ModelInterface
{
    /* define all alert codes */
    const SUPPORT_ALERT_TYPES = [];
    /* const type alert */
    const ALERT_TYPE_INFO = 'info';
    const ALERT_TYPE_WARNING = 'warning';
    const ALERT_TYPE_ERROR = 'error';
    const ALERT_TYPE_ACTION_REQUIRED = 'actionRequired';

    const TARGET_TYPE_CREDIT = 'credit';
    const TARGET_TYPE_PROFILE = 'profile';
    const TARGET_TYPE_AUCTION = 'auction';
    const TARGET_TYPE_PRODUCT_AUCTION = 'product_auction';
    const TARGET_TYPE_PRODUCT = 'product';
    const TARGET_TYPE_BID = 'bid';
    const TARGET_TYPE_BILL = 'bill';
    const TARGET_TYPE_ACCOUNT = 'account';
    const TARGET_TYPE_SALE = 'sale';

    /**
     * @return mixed    
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getCode();

    /**
     * @param mixed $code
     */
    public function setCode($code);

    /**
     * @return mixed
     */
    public function getIsRead();

    /**
     * @param mixed $isRead
     */
    public function setIsRead($isRead);

    /**
     * @return mixed
     */
    public function getCreatedDate();

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate);

    /**
     * @return UserRoleInterface
     */
    public function getAccount();

    /**
     * @param $account
     * @return self
     */
    public function setAccount($account);

    /**
     * @return mixed
     */
    public function getDetail();

    /**
     * @param mixed $detail
     * return self
     */
    public function setDetail($detail);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param mixed $type
     * return self
     */
    public function setType($type);

    /**
     * @return boolean
     */
    public function getIsSent();

    /**
     * @param boolean $isSent
     * @return self
     */
    public function setIsSent($isSent);

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
    public function getTargetType();

    /**
     * @param mixed $targetType
     * @return self
     */
    public function setTargetType($targetType);

    /**
     * @return mixed
     */
    public function getTargetId();

    /**
     * @param mixed $targetId
     * @return self
     */
    public function setTargetId($targetId);
}