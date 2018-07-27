<?php

namespace Biddy\Service\Dashboard\Account;

use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\Role\AccountInterface;

interface UserStatisticsReporterInterface
{
    /**
     * @param AccountInterface $buyer
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getStatisticsByDateRangeForSeller(AccountInterface $buyer, \DateTime $startDate, \DateTime $endDate, $page = 1, $limit = 10);

    /**
     * @param AccountInterface $seller
     * @param ProductInterface $product
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getProductViewsByDateRangeForSeller(AccountInterface $seller, ProductInterface $product, \DateTime $startDate, \DateTime $endDate, $page = 1, $limit = 10);
}