<?php

namespace Biddy\Repository\Product;


use Biddy\Model\User\Role\AccountInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Repository\Core\MultiRepositoryInterface;

interface FreelancerRepositoryInterface extends ObjectRepository, MultiRepositoryInterface
{
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $params
     * @return QueryBuilder
     */
    public function getProductsForUserQuery($user, PagerParam $params);

    /**
     * @param AccountInterface $account
     * @param null $limit
     * @param null $offset
     * @return QueryBuilder
     */
    public function getAuctionsForAccountQuery(AccountInterface $account, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $account
     * @param PagerParam $param
     * @return QueryBuilder
     */
    public function getProductsForUserBiddingQuery(UserRoleInterface $account, PagerParam $param);
}