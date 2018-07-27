<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\ProductManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class ProductHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return ProductManagerInterface
     */
    public function getDomainManager()
    {
        return parent::getDomainManager();
    }
}