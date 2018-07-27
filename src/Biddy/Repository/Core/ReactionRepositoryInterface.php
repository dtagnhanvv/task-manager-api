<?php

namespace Biddy\Repository\Core;


use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\Role\AccountInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;

interface ReactionRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $params
     * @return QueryBuilder
     */
    public function getReactionsForUserQuery(UserRoleInterface $user, PagerParam $params);

    /**
     * @param ProductInterface $product
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function findReactionsByProduct(ProductInterface $product, $page, $limit);

    /**
     * @param CommentInterface $comment
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function findReactionsByComment(CommentInterface $comment, $page, $limit);

    /**
     * @param CommentInterface $comment
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findTotalReactionCountByComment(CommentInterface $comment, $page = 1, $limit = 1);

    /**
     * @param CommentInterface $comment
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findReactionEmotionsByComment(CommentInterface $comment, $page = 1, $limit = 1);

    /**
     * @param ProductInterface $product
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findTotalReactionCountByProduct(ProductInterface $product, $page = 1, $limit = 1);

    /**
     * @param ProductInterface $product
     * @return mixed
     */
    public function findTotalReactionCountByProductGroupByEmotion(ProductInterface $product);

    /**
     * @param CommentInterface $comment
     * @return mixed
     */
    public function findTotalReactionCountByCommentGroupByEmotion(CommentInterface $comment);

    /**
     * @param AccountInterface $user
     * @param $type
     * @param $object
     * @return mixed
     */
    public function findReactionByUserAndObject(AccountInterface $user, $type, $object);
}