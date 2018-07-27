<?php

namespace Biddy\Handler\Handlers\Core;


use Biddy\DomainManager\ProductRatingManagerInterface;
use Biddy\Handler\RoleHandlerAbstract;

abstract class ProductRatingHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @return ProductRatingManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}