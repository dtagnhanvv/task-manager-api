<?php

namespace Biddy\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;

interface BillRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @param array $params
     * @return QueryBuilder
     */
    public function getBillsForUserQuery(UserRoleInterface $user, PagerParam $param, $params = []);

    /**
     * @param UserRoleInterface $user
     * @param $params
     * @return mixed
     */
    public function getSummaryBillStatus(UserRoleInterface $user, $params);
}