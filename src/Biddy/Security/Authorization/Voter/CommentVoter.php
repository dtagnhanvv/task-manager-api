<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\CommentInterface;
use Biddy\Model\User\UserEntityInterface;

class CommentVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = CommentInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param CommentInterface $model
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($model, UserEntityInterface $user, $action)
    {
        if ($action == 'view') {
            return true;
        }

        if (!$user->hasCommentModule()) {
            return false;
        }

        if (!$model->getAuthor()->hasCommentModule()) {
            return false;
        }

        return $user->getId() == $model->getAuthor()->getId();
    }
}