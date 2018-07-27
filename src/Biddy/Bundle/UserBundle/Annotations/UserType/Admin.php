<?php

namespace Biddy\Bundle\UserBundle\Annotations\UserType;

use Doctrine\Common\Annotations\Annotation;

use Biddy\Model\User\Role\AdminInterface;

/**
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class Admin implements UserTypeInterface
{
    public function getUserClass()
    {
        return AdminInterface::class;
    }
}