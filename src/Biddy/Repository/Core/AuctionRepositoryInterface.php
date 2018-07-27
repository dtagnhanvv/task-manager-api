<?php

namespace Biddy\Repository\Core;


use Biddy\Model\Core\ProductInterface;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\Query\QueryBuilder;

interface AuctionRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getAuctionsForUserQuery(UserRoleInterface $user, PagerParam $param);

    /**
     * @param ProductInterface $product
     * @param PagerParam $pagerParam
     * @return QueryBuilder
     */
    public function getAuctionForProductQuery(ProductInterface $product, PagerParam $pagerParam);

    /**
     * @param ProductInterface $product
     * @param \DateTime $date
     * @return mixed
     */
    public function getActiveAuctionForProduct(ProductInterface $product, \DateTime $date);

    /**
     * @param UserRoleInterface $account
     * @param PagerParam $param
     * @return QueryBuilder
     */
    public function getActiveProductsBiddingQuery(UserRoleInterface $account, PagerParam $param);

    /**
     * @param UserRoleInterface $account
     * @param PagerParam $param
     * @return mixed
     */
    public function getAuctionsForUserBiddingQuery(UserRoleInterface $account, PagerParam $param);

    /**
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getAutomatedEndingProducts(\DateTime $endDate);
}