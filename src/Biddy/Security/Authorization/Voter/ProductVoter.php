<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\UserEntityInterface;

class ProductVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = ProductInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param ProductInterface $model
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($model, UserEntityInterface $user, $action)
    {
        if ($action == 'view') {
            return true;
        }

        if (!$user->hasProductModule()) {
            return false;
        }

        return $user->getId() == $model->getSeller()->getId();
    }
}