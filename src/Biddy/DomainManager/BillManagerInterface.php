<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\BillInterface;
use Biddy\Model\User\Role\AccountInterface;

interface BillManagerInterface extends ManagerInterface
{
    /**
     * @param AccountInterface $buyer
     * @param $status
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getBillsForBuyer(AccountInterface $buyer, $status = BillInterface::STATUS_UNCONFIRMED, $page = 1, $limit = 10);
}