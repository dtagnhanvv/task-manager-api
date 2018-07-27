<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\SaleInterface;
use Biddy\Model\User\UserEntityInterface;

class SaleVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = SaleInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param $account
     * @param UserEntityInterface $sale
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($account, UserEntityInterface $sale, $action)
    {
        if ($sale instanceof AdminInterface) {
            return true;
        }

        if ($account->getId() == $sale->getId()) {
            return true;
        }

        return false;
    }
}