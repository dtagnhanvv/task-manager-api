<?php

namespace Biddy\Bundle\UserSystem\SaleBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Biddy\Model\User\Role\SaleInterface;
use Biddy\Model\User\UserEntityInterface;

class SetSaleRoleListener
{
    const ROLE_SALE = 'ROLE_SALE';

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof SaleInterface) {
            return;
        }

        /**
         * @var UserEntityInterface $entity
         */

        $entity->setUserRoles(array(static::ROLE_SALE));
    }
}