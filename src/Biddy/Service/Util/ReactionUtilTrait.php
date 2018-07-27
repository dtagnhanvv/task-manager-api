<?php


namespace Biddy\Service\Util;

use Biddy\DomainManager\ReactionManagerInterface;
use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ProductInterface;

trait ReactionUtilTrait
{
    /**
     * @param ReactionManagerInterface $reactionManager
     * @param $result
     * @param $object
     * @param $user
     * @return mixed
     */
    public function addReactionInfo(ReactionManagerInterface $reactionManager, $result, $object, $user)
    {
        if ($object instanceof ProductInterface) {
            $result['emotions'] = $reactionManager->findTotalReactionCountByProductGroupByEmotion($object);
            $result['total'] = $reactionManager->findTotalReactionCountByProduct($object);
            $result['userReaction'] = $reactionManager->getCurrentReactionByUser($user, 'product', $object);
        }

        if ($object instanceof CommentInterface) {
            $result['emotions'] = $reactionManager->findTotalReactionCountByCommentGroupByEmotion($object);
            $result['total'] = $reactionManager->findTotalReactionCountByComment($object);
            $result['userReaction'] = $reactionManager->getCurrentReactionByUser($user, 'comment', $object);
        }

        return $result;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getReactionType($params)
    {
        $notNullParams = array_keys(array_filter($params));
        $notNullParams = array_intersect($notNullParams, ['product', 'comment']);

        return reset($notNullParams);
    }
}