<?php

namespace Biddy\Repository\Core;


use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ProductInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;

interface CommentRepositoryInterface extends ObjectRepository
{
    /**
     * @param ProductInterface $product
     * @return mixed
     */
    public function findTotalCommentsCountByProduct(ProductInterface $product);

    /**
     * @param CommentInterface $comment
     * @return mixed
     */
    public function findTotalCommentsCountByComment(CommentInterface $comment);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $params
     * @return QueryBuilder
     */
    public function getCommentsForUserQuery(UserRoleInterface $user, PagerParam $params);

    /**
     * @param $user
     * @param ProductInterface $product
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function findCommentsByProduct($user, ProductInterface $product, $page, $limit);

    /**
     * @param CommentInterface $comment
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function findCommentsByComment(CommentInterface $comment, $page, $limit);
}