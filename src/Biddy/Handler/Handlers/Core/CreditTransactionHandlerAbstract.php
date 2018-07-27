<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\CreditTransactionManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class CreditTransactionHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return CreditTransactionManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}