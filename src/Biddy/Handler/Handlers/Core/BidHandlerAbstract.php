<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\BidManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class BidHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return BidManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}