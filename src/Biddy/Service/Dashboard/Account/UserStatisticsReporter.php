<?php

namespace Biddy\Service\Dashboard\Account;

use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\Role\AccountInterface;

class UserStatisticsReporter implements UserStatisticsReporterInterface
{
    /**
     * @inheritdoc
     */
    public function getStatisticsByDateRangeForSeller(AccountInterface $buyer, \DateTime $startDate, \DateTime $endDate, $page = 1, $limit = 10)
    {
        // TODO: Implement getStatisticsByDateRangeForSeller() method.
    }

    /**
     * @inheritdoc
     */
    public function getProductViewsByDateRangeForSeller(AccountInterface $seller, ProductInterface $product, \DateTime $startDate, \DateTime $endDate, $page = 1, $limit = 10)
    {
        // TODO: Implement getProductViewsByDateRangeForSeller() method.
    }
}