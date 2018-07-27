<?php


namespace Biddy\Bundle\SaleApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use Biddy\Exception\LogicException;
use Biddy\Model\User\Role\AccountInterface;

class SetAccountUuidListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AccountInterface) {
            return;
        }

        try {
            $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $entity->getEmail());
            $entity->setUuid($uuid5->toString());
        } catch(UnsatisfiedDependencyException $e) {
            throw new LogicException($e->getMessage());
        }
    }
}