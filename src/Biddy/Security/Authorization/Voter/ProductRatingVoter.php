<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\ProductRatingInterface;
use Biddy\Model\User\UserEntityInterface;

class ProductRatingVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = ProductRatingInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param ProductRatingInterface $modelRating
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($modelRating, UserEntityInterface $user, $action)
    {
        if ($action == 'view') {
            return true;
        }

        return $user->getId() == $modelRating->getReviewer()->getId();
    }
}