<?php

namespace Biddy\Bundle\ApiBundle\Controller;

use Biddy\DomainManager\CommentManagerInterface;
use Biddy\DomainManager\ProductManagerInterface;
use Biddy\DomainManager\ReactionManagerInterface;
use Biddy\Handler\Handlers\Core\ProductHandlerAbstract;
use Biddy\Model\PagerParam;
use Biddy\Service\Util\AccountUtilTrait;
use Biddy\Service\Util\AuctionUtilTrait;
use Biddy\Service\Util\CommentUtilTrait;
use Biddy\Service\Util\ProductUtilTrait;
use Biddy\Service\Util\PublicSimpleException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Biddy\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Biddy\Handler\HandlerInterface;
use Biddy\Model\Core\ProductInterface;

/**
 * @Rest\RouteResource("Product")
 */
class ProductController extends RestControllerAbstract implements ClassResourceInterface
{
    use ProductUtilTrait;
    use GetEntityFromIdTrait;
    use CommentUtilTrait;
    use AccountUtilTrait;
    use AuctionUtilTrait;

    /**
     * Get all products
     *
     * @Rest\View(serializerGroups={"product.detail", "user.summary"})
     *
     * @Rest\QueryParam(name="account", nullable=true, requirements="\d+", description="the account id")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="types", nullable=true, description="the type to get")
     * @Rest\QueryParam(name="type", nullable=true, description="type of product, such as freelancer, professional")
     *
     * @ApiDoc(
     *  section = "Product",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\ProductInterface[]
     */
    public function cgetAction(Request $request)
    {
        $user = $this->getUserDueToQueryParamAccount($request, 'account');

        $productRepository = $this->getProductRepository($request);
        $qb = $productRepository->getProductsForUserQuery($user, $this->getParams());

        $params = array_merge($request->query->all(), $request->attributes->all());
        if (!isset($params['page']) && !isset($params['sortField']) &&
            !isset($params['orderBy']) && !isset($params['searchKey'])
        ) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get all products
     *
     * @Rest\View(serializerGroups={"product_rating.detail", "user.summary"})
     *
     * @Rest\QueryParam(name="account", nullable=true, requirements="\d+", description="the account id")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="types", nullable=true, description="the type to get")
     *
     * @ApiDoc(
     *  section = "Product",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $id
     * @param Request $request
     * @return \Biddy\Model\Core\ProductInterface[]
     */
    public function cgetProductRatingAction($id, Request $request)
    {
        $product = $this->getHandler()->get($id);
        if (!$product instanceof ProductInterface) {
            return new PublicSimpleException("Object not exist");
        }

        $productRatingRepository = $this->get('biddy.repository.product_rating');
        $qb = $productRatingRepository->getProductRatingForProductQuery($product, $this->getParams());

        $params = array_merge($request->query->all(), $request->attributes->all());
        if (!isset($params['page']) && !isset($params['sortField']) &&
            !isset($params['orderBy']) && !isset($params['searchKey'])
        ) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get rating summary of a product
     *
     * @ApiDoc(
     *  section = "Product",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $id
     * @return \Biddy\Model\Core\ProductInterface[]
     */
    public function cgetRatingSummaryAction($id)
    {
        $product = $this->getHandler()->get($id);
        if (!$product instanceof ProductInterface) {
            return new PublicSimpleException("Object not exist");
        }

        $productRatingRepository = $this->get('biddy.repository.product_rating');

        $result['total'] = $productRatingRepository->findTotalProductRatingByProduct($product);
        $result['average'] = number_format($product->getRating(), 1);
        $result['detail'] = $productRatingRepository->findDetailRatingByProduct($product);

        return $result;
    }

    /**
     * Get all products
     * @Rest\Post("/products/list")
     * @Rest\View(serializerGroups={"product.detail", "freelancer.detail", "professional.detail", "user.summary", "product_tag.minimum", "tag.minimum"})
     *
     * @Rest\QueryParam(name="account", nullable=true, requirements="\d+", description="the account id")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="types", nullable=true, description="the type to get")
     * @Rest\QueryParam(name="filters", nullable=true, description="the type to get")
     *
     * @ApiDoc(
     *  section = "Product",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\ProductInterface[]
     */
    public function postProductsAction(Request $request)
    {
        $productRepository = $this->getProductRepository($request);
        $user = $this->getUserDueToQueryParamAccount($request, 'account');
        $params = array_merge($request->request->all(), $request->query->all());
        $request->query->add($request->request->all());
        $pagerParams = $this->_createPagerParams($params);

        $qb = $productRepository->getProductsForUserQuery($user, $pagerParams);

        $pagination = $this->getPagination($qb, $request);
        $pagination['records'] = $this->serializeProducts($pagination['records']);

        return $pagination;
    }

    /**
     * Get bids for current account
     * @Rest\View(
     *      serializerGroups={"user.minimum"}
     * )
     * @param Request $request
     * @return \Biddy\Bundle\UserBundle\Entity\User
     */
    public function postBuyerAction(Request $request)
    {
        $account = $this->getUserDueToQueryParamAccount($request, 'account');
        $auctionRepository = $this->get('biddy.repository.auction');
        $bidRepository = $this->get('biddy.repository.bid');

        $params = array_merge($request->query->all(), $request->request->all());
        $request->query->add($params);

        $qb = $auctionRepository->getAuctionsForUserBiddingQuery($account, $this->_createPagerParams($params));
        $pagination = $this->getPagination($qb, $request);
        $pagination['records'] = $this->serializeAuctions($pagination['records'], $account, $bidRepository);

        return $pagination;
    }

    /**
     * Get active bids for current account
     * @Rest\View(
     *      serializerGroups={"user.minimum"}
     * )
     * @param Request $request
     * @return \Biddy\Bundle\UserBundle\Entity\User
     */
    public function postActiveAction(Request $request)
    {
        $account = $this->getUserDueToQueryParamAccount($request, 'account');
        $auctionRepository = $this->get('biddy.repository.auction');
        $bidRepository = $this->get('biddy.repository.bid');

        $params = array_merge($request->query->all(), $request->request->all());
        $request->query->add($params);

        $qb = $auctionRepository->getActiveProductsBiddingQuery($account, $this->_createPagerParams($params));
        $pagination = $this->getPagination($qb, $request);
        $pagination['records'] = $this->serializeAuctions($pagination['records'], $account, $bidRepository);

        return $pagination;
    }

    /**
     * Get a single product group for the given id
     *
     * @Rest\View(serializerGroups={"product.detail", "freelancer.detail", "professional.detail", "auction.summary", "user.summary", "reaction.minimum", "product_tag.minimum", "tag.minimum"})
     *
     * @ApiDoc(
     *  section = "Product",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return ProductInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        /** @var ProductInterface $product */
        $product = $this->one($id);

        return $this->serializeProduct($product);
    }

    /**
     * Get comments of a single product group for the given id
     *
     * @Rest\View(serializerGroups={"comment.minimum", "user.minimum"})
     *
     * @ApiDoc(
     *  section = "Product",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @param Request $request
     * @return ProductInterface
     */
    public function getCommentsAction($id, Request $request)
    {
        $product = $this->one($id);
        if (!$product instanceof ProductInterface) {
            return [];
        }

        $params = array_merge($request->request->all(), $request->query->all());
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 10;

        /** @var CommentManagerInterface $commentManager */
        $commentManager = $this->get('biddy.domain_manager.comment');
        /** @var ReactionManagerInterface $reactionManager */
        $reactionManager = $this->get('biddy.domain_manager.reaction');

        $comments = $commentManager->findCommentsByProduct($this->getUser(), $product, $page, $limit);
        $groups = $this->serializeComments($comments, $commentManager, $reactionManager);

        $count = $commentManager->findTotalCommentsCountByProduct($product);
        $next = sprintf("%s%s?limit=%s&page=%s", $request->getBaseUrl(), $request->getPathInfo(), $limit, $page + 1);

        return [
            'count' => $count,
            'next' => $next,
            'previous' => $request->getRequestUri(),
            'results' => $groups
        ];
    }

    /**
     * Create a product from the submitted data
     * @Rest\View(serializerGroups={"product.minimum", "user.minimum"})
     * @ApiDoc(
     *  section = "Product",
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
        return $this->postAndReturnEntityData($request);
    }

    /**
     * Update an existing product from the submitted data or create a new ad network
     *
     * @ApiDoc(
     *  section = "Product",
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
     * Update an existing product from the submitted data or create a new product at a specific location
     *
     * @ApiDoc(
     *  section = "Product",
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
     * @return string
     */
    protected function getResourceName()
    {
        return 'product';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_product';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_api.handler.product');
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
        return new PagerParam($params[PagerParam::PARAM_SEARCHES],
            $params[PagerParam::PARAM_SEARCH_FIELD], $params[PagerParam::PARAM_SEARCH_KEY],
            $params[PagerParam::PARAM_SORT_FIELD], $params[PagerParam::PARAM_SORT_DIRECTION],
            $accountId, $params[PagerParam::PARAM_PAGE], $params[PagerParam::PARAM_LIMIT]);
    }

    /**
     * @param Request $request
     * @return ProductManagerInterface
     * @throws PublicSimpleException
     */
    private function getProductManager(Request $request)
    {
        $handler = $this->getHandler();
        if (!$handler instanceof ProductHandlerAbstract) {
            throw new PublicSimpleException($this->getParameter('none_handler_accept_this request'));
        }

        return $handler->getDomainManager();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws PublicSimpleException
     */
    private function getProductRepository(Request $request)
    {
        $domainManager = $this->getProductManager($request);
        if (!$domainManager instanceof ProductManagerInterface) {
            throw new PublicSimpleException($this->getParameter('none_handler_accept_this request'));
        }

        return $domainManager->getRepositoryByModel($request);
    }
}