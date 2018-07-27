<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\WalletInterface;
use Biddy\Model\User\UserEntityInterface;

class WalletVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = WalletInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param WalletInterface $alert
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($alert, UserEntityInterface $user, $action)
    {
        return $user->getId() == $alert->getOwner()->getId();
    }
}