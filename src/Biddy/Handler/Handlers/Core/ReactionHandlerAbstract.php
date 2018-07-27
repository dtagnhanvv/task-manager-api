<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\ReactionManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class ReactionHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return ReactionManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}