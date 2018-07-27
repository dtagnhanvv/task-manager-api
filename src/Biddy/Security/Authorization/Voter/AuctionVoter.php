<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\User\UserEntityInterface;

class AuctionVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = AuctionInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param AuctionInterface $model
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

        return $user->getId() == $model->getProduct()->getSeller()->getId();
    }
}