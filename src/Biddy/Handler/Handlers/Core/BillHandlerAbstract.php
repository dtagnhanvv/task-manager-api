<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\BillManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class BillHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return BillManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}