<?php

namespace Biddy\Bundle\SaleApiBundle\Handler;

use Biddy\Bundle\UserBundle\DomainManager\SaleManagerInterface;
use Biddy\Handler\HandlerAbstract;

/**
 * Not using a RoleHandlerInterface because this Handler is local
 * to the sale api bundle. All routes to this bundle are protected in app/config/security.yml
 */
class SaleHandler extends HandlerAbstract implements SaleHandlerInterface
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return SaleManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }

    /**
     * @inheritdoc
     */
    public function allSales()
    {
        return $this->getDomainManager()->allSales();
    }

    public function allActiveSales()
    {
        return $this->getDomainManager()->allActiveSales();
    }
}