<?php

namespace Biddy\Bundle\UserSystem\AccountBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\UserEntityInterface;

class SetAccountRoleListener
{
    const ROLE_ACCOUNT = 'ROLE_ACCOUNT';

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AccountInterface) {
            return;
        }

        /**
         * @var UserEntityInterface $entity
         */

        $entity->setUserRoles(array(static::ROLE_ACCOUNT));
    }
}