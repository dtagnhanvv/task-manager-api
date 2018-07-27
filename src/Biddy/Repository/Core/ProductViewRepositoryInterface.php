<?php

namespace Biddy\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;

interface ProductViewRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $params
     * @return QueryBuilder
     */
    public function getProductViewsForUserQuery(UserRoleInterface $user, PagerParam $params);
}