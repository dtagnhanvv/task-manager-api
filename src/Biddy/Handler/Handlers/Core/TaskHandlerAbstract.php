<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\TaskManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class TaskHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return TaskManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}