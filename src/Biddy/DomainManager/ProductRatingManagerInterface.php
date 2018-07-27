<?php

namespace Biddy\DomainManager;

use Biddy\Model\User\Role\AccountInterface;

interface ProductRatingManagerInterface extends ManagerInterface
{
    /**
     * @param AccountInterface $buyer
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getCurrentProductRatingsForBuyer(AccountInterface $buyer, $page = 1, $limit = 10);

    /**
     * @param AccountInterface $seller
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getProductRatingsForBuyer(AccountInterface $seller, $page = 1, $limit = 10);
}