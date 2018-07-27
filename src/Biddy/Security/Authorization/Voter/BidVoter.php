<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\BidInterface;
use Biddy\Model\User\UserEntityInterface;

class BidVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = BidInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param BidInterface $bid
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($bid, UserEntityInterface $user, $action)
    {
        return $user->getId() == $bid->getBuyer()->getId();
    }
}