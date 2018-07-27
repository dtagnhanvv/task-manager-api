<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\ReactionInterface;
use Biddy\Model\User\UserEntityInterface;

class ReactionVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = ReactionInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param ReactionInterface $reaction
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($reaction, UserEntityInterface $user, $action)
    {
        if ($action == 'view') {
            return true;
        }

        return $user->getId() == $reaction->getViewer()->getId();
    }
}