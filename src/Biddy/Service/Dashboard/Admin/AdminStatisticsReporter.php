<?php

namespace Biddy\Service\Dashboard\Admin;

use Biddy\Bundle\UserBundle\Annotations\UserType\Account;

class AdminStatisticsReporter implements AdminStatisticsReporterInterface
{
    /**
     * @inheritdoc
     */
    public function getStatisticsByDateRange(\DateTime $startDate, \DateTime $endDate)
    {

    }

    /**
     * @inheritdoc
     */
    public function getUserStatisticsByDateRange(Account $account, \DateTime $startDate, \DateTime $endDate)
    {

    }
}