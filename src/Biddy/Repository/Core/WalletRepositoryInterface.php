<?php

namespace Biddy\Repository\Core;


use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;

interface WalletRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $params
     * @return QueryBuilder
     */
    public function getWalletsForUserQuery(UserRoleInterface $user, PagerParam $params);
}