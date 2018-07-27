<?php

namespace Biddy\Bundle\ApiBundle\Controller;

use Biddy\DomainManager\CommentManagerInterface;
use Biddy\DomainManager\ReactionManagerInterface;
use Biddy\Model\Core\ReactionInterface;
use Biddy\Model\PagerParam;
use Biddy\Service\Util\CommentUtilTrait;
use Doctrine\Common\Collections\Collection;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Biddy\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Biddy\Handler\HandlerInterface;
use Biddy\Model\Core\CommentInterface;

/**
 * @Rest\RouteResource("Comment")
 */
class CommentController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;
    use CommentUtilTrait;

    /**
     * Get all comments
     *
     * @Rest\View(serializerGroups={"comment.detail", "user.summary", "reaction.minimum"})
     *
     * @Rest\QueryParam(name="account", nullable=true, requirements="\d+", description="the account id")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="types", nullable=true, description="the type to get")
     *
     * @ApiDoc(
     *  section = "Comment",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\CommentInterface[]
     */
    public function cgetAction(Request $request)
    {
        $user = $this->getUserDueToQueryParamAccount($request, 'account');

        $commentRepository = $this->get('biddy.repository.comment');
        $qb = $commentRepository->getCommentsForUserQuery($user, $this->getParams());

        $params = array_merge($request->query->all(), $request->attributes->all());
        if (!isset($params['page']) && !isset($params['sortField']) && !isset($params['orderBy']) && !isset($params['searchKey'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get all comments
     * @Rest\Post("/comments/list")
     * @Rest\View(serializerGroups={"comment.detail", "user.summary", "reaction.minimum"})
     *
     * @Rest\QueryParam(name="account", nullable=true, requirements="\d+", description="the account id")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="types", nullable=true, description="the type to get")
     * @Rest\QueryParam(name="filters", nullable=true, description="the type to get")
     *
     * @ApiDoc(
     *  section = "Comment",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\CommentInterface[]
     */
    public function postCommentsAction(Request $request)
    {
        $commentRepository = $this->get('biddy.repository.comment');
        $user = $this->getUserDueToQueryParamAccount($request, 'account');
        $params = array_merge($request->request->all(), $request->query->all());
        $request->query->add($request->request->all());
        $pagerParams = $this->_createPagerParams($params);

        $qb = $commentRepository->getCommentsForUserQuery($user, $pagerParams);

        if (!isset($params['page'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get a single comment group for the given id
     *
     * @Rest\View(serializerGroups={"comment.detail", "user.summary", "reaction.minimum"})
     *
     * @ApiDoc(
     *  section = "Comment",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return CommentInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get comments of a single comments for the given id
     *
     * @Rest\View(serializerGroups={"comment.minimum", "comment_order.minimum", "user.minimum"})
     *
     * @ApiDoc(
     *  section = "Comment",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @param Request $request
     * @return CommentInterface
     */
    public function getChildrensAction($id, Request $request)
    {
        $comment = $this->one($id);

        if (!$comment instanceof CommentInterface) {
            return [];
        }

        $params = array_merge($request->request->all(), $request->query->all());
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;

        /** @var CommentManagerInterface $commentManager */
        $commentManager = $this->get('biddy.domain_manager.comment');
        /** @var ReactionManagerInterface $reactionManager */
        $reactionManager = $this->get('biddy.domain_manager.reaction');

        $comments = $commentManager->findCommentsByComment($comment, $page, $limit);
        $groups = $this->serializeComments($comments, $commentManager, $reactionManager, $params);

        $count = $commentManager->findTotalCommentsCountByComment($comment);
        $next = sprintf("limit=%s&page=%s", $request->getBaseUrl(), $request->getPathInfo(), $limit, $page + 1);

        return [
            'count' => $count,
            'next' => $next,
            'previous' => $request->getRequestUri(),
            'results' => $groups
        ];
    }

    /**
     * Get reactions of a single comment for the given id
     *
     * @Rest\View(serializerGroups={"reaction.minimum", "user.minimum"})
     *
     * @ApiDoc(
     *  section = "Comment",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return ReactionInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getReactionsAction($id, Request $request)
    {
        $comment = $this->one($id);
        if (!$comment instanceof CommentInterface) {
            return [];
        }

        $reactions = $comment->getReactions();
        $reactions = $reactions instanceof Collection ? $reactions->toArray() : $reactions;
        $maps = [];

        foreach ($reactions as $reaction) {
            if (!$reaction instanceof ReactionInterface) {
                continue;
            }
            $emotion = $reaction->getEmotion();

            //Add to queue
            $maps['all']['detail'][] = $reaction;
            $maps[$emotion]['detail'][] = $reaction;

            //Counting
            $maps['all']['count'] = isset($maps['all']['count']) ? $maps['all']['count'] + 1 : 1;
            $maps[$emotion]['count'] = isset($maps[$emotion]['count']) ? $maps[$emotion]['count'] + 1 : 1;
        }

        return $maps;
    }

    /**
     * Create a comment from the submitted data
     *
     * @ApiDoc(
     *  section = "Comment",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postAction(Request $request)
    {
        return $this->post($request);
    }

    /**
     * Update an existing comment from the submitted data or create a new ad network
     *
     * @ApiDoc(
     *  section = "Comment",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when the resource is created",
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function putAction(Request $request, $id)
    {
        return $this->put($request, $id);
    }

    /**
     * Update an existing comment from the submitted data or create a new comment at a specific location
     *
     * @ApiDoc(
     *  section = "Comment",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when resource not exist
     */
    public function patchAction(Request $request, $id)
    {
        return $this->patch($request, $id);
    }

    /**
     * Delete an existing comment
     *
     * @ApiDoc(
     *  section = "Comment",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return View
     *
     * @throws NotFoundHttpException when the resource not exist
     */
    public function deleteAction($id)
    {
        return $this->delete($id);
    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'comment';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_comment';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_api.handler.comment');
    }

    /**
     * @var array $params
     * @return PagerParam
     */
    protected function _createPagerParams(array $params)
    {
        // create a params array with all values set to null
        $defaultParams = array_fill_keys([
            PagerParam::PARAM_SEARCH_FIELD,
            PagerParam::PARAM_SEARCH_KEY,
            PagerParam::PARAM_SORT_FIELD,
            PagerParam::PARAM_SORT_DIRECTION,
            PagerParam::PARAM_ACCOUNT_ID,
            PagerParam::PARAM_PAGE,
            PagerParam::PARAM_LIMIT,
        ], null);

        $defaultParams[PagerParam::PARAM_SEARCHES] = [];
        $params = array_merge($defaultParams, $params);
        $accountId = intval($params[PagerParam::PARAM_ACCOUNT_ID]);
        return new PagerParam($params[PagerParam::PARAM_SEARCHES], $params[PagerParam::PARAM_SEARCH_FIELD], $params[PagerParam::PARAM_SEARCH_KEY], $params[PagerParam::PARAM_SORT_FIELD], $params[PagerParam::PARAM_SORT_DIRECTION], $accountId, $params[PagerParam::PARAM_PAGE], $params[PagerParam::PARAM_LIMIT]);
    }
}