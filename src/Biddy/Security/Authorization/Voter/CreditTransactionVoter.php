<?php

namespace Biddy\Security\Authorization\Voter;

use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\User\UserEntityInterface;

class CreditTransactionVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = CreditTransactionInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param CreditTransactionInterface $creditTransaction
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isAccountActionAllowed($creditTransaction, UserEntityInterface $user, $action)
    {
        if ($action == 'view') {
            return true;
        }

        if ($action == 'edit') {
            return false;
        }

        return $user->getId() == $creditTransaction->getFromWallet()->getId();
    }
}