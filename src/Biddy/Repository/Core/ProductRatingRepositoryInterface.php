<?php

namespace Biddy\Repository\Core;


use Biddy\Model\Core\BillInterface;
use Biddy\Model\Core\ProductInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;

interface ProductRatingRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserRoleInterface $user
     * @param $product
     * @param $bill
     * @param PagerParam $params
     * @return QueryBuilder
     */
    public function getProductRatingsForUserQuery(UserRoleInterface $user, $product, $bill, PagerParam $params);

    /**
     * @param ProductInterface $product
     * @param PagerParam $pagerParam
     * @return mixed
     */
    public function getProductRatingForProductQuery(ProductInterface $product, PagerParam $pagerParam);

    /**
     * @param ProductInterface $product
     * @return mixed
     */
    public function findTotalProductRatingByProduct(ProductInterface $product);

    /**
     * @param ProductInterface $product
     * @return mixed
     */
    public function findDetailRatingByProduct(ProductInterface $product);

    /**
     * @param UserRoleInterface $user
     * @param BillInterface $bill
     * @return QueryBuilder
     */
    public function getProductRatingForBillQuery(UserRoleInterface $user, BillInterface $bill);
}