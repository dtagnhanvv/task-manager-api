<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\ProductViewInterface;
use Biddy\Model\User\UserEntityInterface;

class ProductViewVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = ProductViewInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param ProductViewInterface $modelView
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($modelView, UserEntityInterface $user, $action)
    {
        return $user->getId() == $modelView->getViewer()->getId();
    }
}