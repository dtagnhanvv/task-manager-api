<?php

namespace Biddy\Model\User\Role;

use Biddy\Model\User\UserEntityInterface;

interface UserRoleInterface extends UserEntityInterface
{
    /**
     * @return UserEntityInterface
     */
    public function getUser();

    /**
     * @return int|null
     */
    public function getId();
}