<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\BillInterface;
use Biddy\Model\User\UserEntityInterface;

class BillVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = BillInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param BillInterface $bill
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($bill, UserEntityInterface $user, $action)
    {
        if ($action == 'view') {
            return true;
        }

        if ($user->getId() == $bill->getSeller()->getId()) {
            return true;
        }

        if ($user->getId() == $bill->getBuyer()->getId()) {
            return true;
        }

        return false;
    }
}