<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\AlertInterface;
use Biddy\Model\User\UserEntityInterface;

class AlertVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = AlertInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param AlertInterface $alert
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($alert, UserEntityInterface $user, $action)
    {
        return $user->getId() == $alert->getAccount()->getId();
    }
}