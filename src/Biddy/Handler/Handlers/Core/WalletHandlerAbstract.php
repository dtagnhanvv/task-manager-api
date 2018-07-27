<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\WalletManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class WalletHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return WalletManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}