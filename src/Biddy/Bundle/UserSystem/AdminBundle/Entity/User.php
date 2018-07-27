<?php

namespace Biddy\Bundle\UserSystem\AdminBundle\Entity;

use Biddy\Bundle\UserBundle\Entity\User as BaseUser;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\UserEntityInterface;

class User extends BaseUser implements AdminInterface
{
    protected $id;
    protected $settings;

    /**
     * @return UserEntityInterface
     */
    public function getUser()
    {
        return $this;
    }
}
