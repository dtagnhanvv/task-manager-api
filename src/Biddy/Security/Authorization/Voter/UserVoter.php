<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\UserEntityInterface;

class UserVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = AccountInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param $account
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($account, UserEntityInterface $user, $action)
    {
        if ($user instanceof AdminInterface) {
            return true;
        }

        if ($account->getId() == $user->getId()) {
            return true;
        }

        return false;
    }
}