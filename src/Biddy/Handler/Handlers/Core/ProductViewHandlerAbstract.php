<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\ProductViewManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class ProductViewHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return ProductViewManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}