<?php

namespace Biddy\Service\Dashboard\Admin;

use Biddy\Bundle\UserBundle\Annotations\UserType\Account;

interface AdminStatisticsReporterInterface
{
    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getStatisticsByDateRange(\DateTime $startDate, \DateTime $endDate);

    /**
     * @param Account $account
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getUserStatisticsByDateRange(Account $account, \DateTime $startDate, \DateTime $endDate);
}