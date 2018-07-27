<?php


namespace Biddy\Service\Util;


use Biddy\DomainManager\CommentManagerInterface;
use Biddy\DomainManager\ReactionManagerInterface;
use Biddy\Model\Core\CommentInterface;

trait CommentUtilTrait
{
    /**
     * @param $comments
     * @param CommentManagerInterface $commentManager
     * @param ReactionManagerInterface $reactionManager
     * @param $params
     * @return array
     */
    function serializeComments($comments, CommentManagerInterface $commentManager, ReactionManagerInterface $reactionManager, $params = [])
    {
        $groups = [];
        foreach ($comments as $comment) {
            if (!$comment instanceof CommentInterface) {
                continue;
            }
            $group = [];
            $group['author'] = $comment->getAuthor();
            $group['id'] = $comment->getId();
            $group['content'] = $comment->getContent();
            $group['modified'] = $comment->getModified();
            $group['createdDate'] = $comment->getCreatedDate();
            $childComments = $commentManager->findCommentsByComment($comment);
            $group['childComments'] = $this->serializeComments($childComments, $commentManager, $reactionManager, $params);
            $group['reactions']['total'] = $reactionManager->findTotalReactionCountByComment($comment);
            $group['reactions']['emotion'] = $reactionManager->findTotalReactionCountByCommentGroupByEmotion($comment);
            
            //Advance: 
            $limit = isset($params['limit']) ? $params['limit'] : 10;
            $page = isset($params['page']) ? $params['page'] : 1;
            $nextPage = $page + 1;
            $group['nextPage'] = $nextPage;
            $group['nextLimit'] = $limit;
            $groups[] = $group;
        }

        return $groups;
    }
}