<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ProductInterface;

interface CommentManagerInterface extends ManagerInterface
{
    /**
     * @param ProductInterface $product
     */
    public function findTotalCommentsCountByProduct(ProductInterface $product);

    /**
     * @param CommentInterface $comment
     * @return mixed
     */
    public function findTotalCommentsCountByComment(CommentInterface $comment);
    
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
    public function findCommentsByComment(CommentInterface $comment, $page = 1, $limit = 10);
}