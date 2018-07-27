<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;

interface ReactionManagerInterface extends ManagerInterface
{
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

    /**
     * @param $user
     * @param $type
     * @param ModelInterface $object
     * @return mixed
     */
    public function getCurrentReactionByUser($user, $type, ModelInterface $object);
}