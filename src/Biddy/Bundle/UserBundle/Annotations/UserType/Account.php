<?php

namespace Biddy\Bundle\UserBundle\Annotations\UserType;

use Doctrine\Common\Annotations\Annotation;

use Biddy\Model\User\Role\AccountInterface;

/**
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class Account implements UserTypeInterface
{
    public function getUserClass()
    {
        return AccountInterface::class;
    }
}