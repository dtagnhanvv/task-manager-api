<?php

namespace Biddy\DomainManager;

use Biddy\Model\User\Role\AccountInterface;

interface BidManagerInterface extends ManagerInterface
{
    /**
     * @param AccountInterface $buyer
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getCurrentBidsForBuyer(AccountInterface $buyer, $page = 1, $limit = 10);
}