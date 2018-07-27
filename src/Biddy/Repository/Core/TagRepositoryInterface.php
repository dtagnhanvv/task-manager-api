<?php

namespace Biddy\Repository\Core;


use Biddy\Model\Core\TagInterface;
use Biddy\Model\Core\ProductInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;

interface TagRepositoryInterface extends ObjectRepository
{
    /**
     * @param ProductInterface $product
     * @return mixed
     */
    public function findTotalTagsCountByProduct(ProductInterface $product);

    /**
     * @param TagInterface $tag
     * @return mixed
     */
    public function findTotalTagsCountByTag(TagInterface $tag);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $params
     * @return QueryBuilder
     */
    public function getTagsForUserQuery(UserRoleInterface $user, PagerParam $params);

    /**
     * @param ProductInterface $product
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function findTagsByProduct(ProductInterface $product, $page, $limit);

    /**
     * @param TagInterface $tag
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function findTagsByTag(TagInterface $tag, $page, $limit);
}