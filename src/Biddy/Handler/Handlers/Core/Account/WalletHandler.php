<?php

namespace Biddy\Handler\Handlers\Core\Account;


use Biddy\Exception\LogicException;
use Biddy\Handler\Handlers\Core\WalletHandlerAbstract;
use Biddy\Model\Core\WalletInterface;
use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;

class WalletHandler extends WalletHandlerAbstract
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
    protected function processForm(ModelInterface $wallet, array $parameters, $method = "PUT")
    {
        /** @var WalletInterface $wallet */
        if (null == $wallet->getOwner()) {
            $wallet->setOwner($this->getUserRole());
        }

        return parent::processForm($wallet, $parameters, $method);
    }
}