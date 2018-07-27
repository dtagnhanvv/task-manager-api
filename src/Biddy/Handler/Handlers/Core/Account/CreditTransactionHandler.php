<?php

namespace Biddy\Handler\Handlers\Core\Account;


use Biddy\Exception\LogicException;
use Biddy\Handler\Handlers\Core\CreditTransactionHandlerAbstract;
use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;

class CreditTransactionHandler extends CreditTransactionHandlerAbstract
{
    /**
     * @inheritdoc
     */
    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof AccountInterface;
    }

    /**
     * @inheritdoc
     * @return AccountInterface
     * @throws LogicException
     */
    public function getUserRole()
    {
        $role = parent::getUserRole();

        if (!$role instanceof AccountInterface) {
            throw new LogicException('userRole does not implement AccountInterface');
        }

        return $role;
    }

    public function all($limit = null, $offset = null)
    {
        return $this->getDomainManager()->all($limit, $offset);
    }

    /**
     * @inheritdoc
     */
    protected function processForm(ModelInterface $creditTransaction, array $parameters, $method = "PUT")
    {
        /** @var CreditTransactionInterface $creditTransaction */
        if (null == $creditTransaction->getFromWallet()) {
            $creditTransaction->setFromWallet($this->getUserRole());
        }

        return parent::processForm($creditTransaction, $parameters, $method);
    }
}