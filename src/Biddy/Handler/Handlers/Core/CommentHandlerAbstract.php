<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\CommentManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class CommentHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return CommentManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}