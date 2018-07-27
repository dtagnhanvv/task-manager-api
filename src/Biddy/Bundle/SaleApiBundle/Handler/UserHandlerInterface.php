<?php

namespace Biddy\Bundle\SaleApiBundle\Handler;

use Biddy\Handler\HandlerInterface;

interface UserHandlerInterface extends HandlerInterface
{
    /**
     * @return array
     */
    public function allAccounts();

    public function allActiveAccounts();
}