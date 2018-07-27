<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\Alert as AlertModel;
use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Model\User\UserEntityInterface;

class Alert extends AlertModel
{
    protected $id;
    protected $code;
    protected $isRead;
    protected $detail;
    protected $createdDate;
    protected $type;
    protected $isSent;
    protected $deletedAt;

    protected $targetType;
    protected $targetId;

    /**
     * @var UserRoleInterface
     */
    protected $account;

    /**
     * @inheritdoc
     *
     * inherit constructor for inheriting all default initialized value
     */
    public function __construct()
    {
        parent::__construct();
    }
}