<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\TagInterface;
use Biddy\Model\User\UserEntityInterface;

class TagVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = TagInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TagInterface $tag
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($tag, UserEntityInterface $user, $action)
    {
        return true;
    }
}