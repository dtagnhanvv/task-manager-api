<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\AlertManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class AlertHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return AlertManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}