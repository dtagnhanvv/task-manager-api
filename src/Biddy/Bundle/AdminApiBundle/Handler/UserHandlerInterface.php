<?php

namespace Biddy\Bundle\AdminApiBundle\Handler;

use Biddy\Handler\HandlerInterface;

interface UserHandlerInterface extends HandlerInterface
{
    /**
     * @return array
     */
    public function allAccounts();

    public function allActiveAccounts();
}