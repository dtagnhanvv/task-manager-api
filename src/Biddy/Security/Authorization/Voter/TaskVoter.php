<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\TaskInterface;
use Biddy\Model\User\UserEntityInterface;

class TaskVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = TaskInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TaskInterface $task
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($task, UserEntityInterface $user, $action)
    {
        return $user->getId() == $task->getOwner()->getId();
    }
}