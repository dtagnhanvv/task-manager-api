<?php

namespace Biddy\Bundle\ApiBundle\Controller;

use Biddy\Model\PagerParam;
use Biddy\Service\Util\CommentUtilTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Biddy\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Biddy\Handler\HandlerInterface;
use Biddy\Model\Core\TagInterface;

/**
 * @Rest\RouteResource("Tag")
 */
class TagController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;
    use CommentUtilTrait;

    /**
     * Get all tags
     *
     * @Rest\View(serializerGroups={"tag.detail", "user.summary"})
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
     *  section = "Tag",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\TagInterface[]
     */
    public function cgetAction(Request $request)
    {
        $user = $this->getUserDueToQueryParamAccount($request, 'account');

        $tagRepository = $this->get('biddy.repository.tag');
        $qb = $tagRepository->getTagsForUserQuery($user, $this->getParams());

        $params = array_merge($request->query->all(), $request->attributes->all());
        if (!isset($params['page']) && !isset($params['sortField']) && !isset($params['orderBy']) && !isset($params['searchKey'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get all tags
     * @Rest\Post("/tags/list")
     * @Rest\View(serializerGroups={"tag.detail", "user.summary"})
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
     *  section = "Tag",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\TagInterface[]
     */
    public function postTagsAction(Request $request)
    {
        $tagRepository = $this->get('biddy.repository.tag');
        $user = $this->getUserDueToQueryParamAccount($request, 'account');
        $params = array_merge($request->request->all(), $request->query->all());
        $request->query->add($request->request->all());
        $pagerParams = $this->_createPagerParams($params);

        $qb = $tagRepository->getTagsForUserQuery($user, $pagerParams);

        if (!isset($params['page'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get a single tag group for the given id
     *
     * @Rest\View(serializerGroups={"tag.detail", "user.summary", "reaction.minimum"})
     *
     * @ApiDoc(
     *  section = "Tag",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return TagInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a tag from the submitted data
     *
     * @ApiDoc(
     *  section = "Tag",
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
     * Update an existing tag from the submitted data or create a new ad network
     *
     * @ApiDoc(
     *  section = "Tag",
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
     * Update an existing tag from the submitted data or create a new tag at a specific location
     *
     * @ApiDoc(
     *  section = "Tag",
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
     * Delete an existing tag
     *
     * @ApiDoc(
     *  section = "Tag",
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
        return 'tag';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_tag';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_api.handler.tag');
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