<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\AuctionManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class AuctionHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return AuctionManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}