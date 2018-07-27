<?php

namespace Biddy\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;

interface AlertRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $params
     * @return QueryBuilder
     */
    public function getAlertsForUserQuery(UserRoleInterface $user, PagerParam $params);
    

    /**
     * @param AccountInterface $account
     * @param array $types
     * @return array
     */
    public function getAlertsToSendEmailByTypesQuery(AccountInterface $account, array $types);

    /**
     * @param $ids
     * @return mixed
     */
    public function deleteAlertsByIds($ids = null);

    /**
     * @param $ids
     * @return mixed
     */
    public function updateMarkAsReadByIds($ids=null);

    /**
     * @param $ids
     * @return mixed
     */
    public function updateMarkAsUnreadByIds($ids=null);

    /**
     * @param UserRoleInterface $user
     * @return mixed
     */
    public function getTotalUnread(UserRoleInterface $user);

    /**
     * @param UserRoleInterface $user
     * @return mixed
     */
    public function readAll(UserRoleInterface $user);
}