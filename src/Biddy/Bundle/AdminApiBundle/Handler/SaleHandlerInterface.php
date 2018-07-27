<?php

namespace Biddy\Bundle\AdminApiBundle\Handler;

use Biddy\Handler\HandlerInterface;

interface SaleHandlerInterface extends HandlerInterface
{
    /**
     * @return array
     */
    public function allSales();

    public function allActiveSales();
}