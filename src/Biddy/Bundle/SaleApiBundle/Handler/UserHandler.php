<?php

namespace Biddy\Bundle\SaleApiBundle\Handler;

use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\Handler\HandlerAbstract;

/**
 * Not using a RoleHandlerInterface because this Handler is local
 * to the sale api bundle. All routes to this bundle are protected in app/config/security.yml
 */
class UserHandler extends HandlerAbstract implements UserHandlerInterface
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return AccountManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }

    /**
     * @inheritdoc
     */
    public function allAccounts()
    {
        return $this->getDomainManager()->allAccounts();
    }

    public function allActiveAccounts()
    {
        return $this->getDomainManager()->allActiveAccounts();
    }
}