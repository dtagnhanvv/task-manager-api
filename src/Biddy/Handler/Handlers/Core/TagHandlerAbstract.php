<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\TagManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class TagHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return TagManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}