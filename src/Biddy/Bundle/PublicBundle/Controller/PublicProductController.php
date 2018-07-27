<?php

namespace Biddy\Bundle\PublicBundle\Controller;

use Biddy\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\Bundle\UserBundle\DomainManager\AdminManagerInterface;
use Biddy\Bundle\UserBundle\DomainManager\SaleManagerInterface;
use Biddy\DomainManager\CommentManagerInterface;
use Biddy\DomainManager\ProductManagerInterface;
use Biddy\DomainManager\ReactionManagerInterface;
use Biddy\Handler\HandlerInterface;
use Biddy\Handler\Handlers\Core\ProductHandlerAbstract;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\SaleInterface;
use Biddy\Service\Util\CommentUtilTrait;
use Biddy\Service\Util\ProductUtilTrait;
use Biddy\Service\Util\PublicSimpleException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Rest\RouteResource("Product")
 */
class PublicProductController extends RestControllerAbstract implements ClassResourceInterface
{
    use CommentUtilTrait;
    use ProductUtilTrait;

    /**
     * Get all products
     * @Rest\Post("/products/list")
     * @Rest\View(serializerGroups={"product.detail", "user.summary", "product_tag.minimum", "tag.minimum"})
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
    public function postProductsAction(Request $request)
    {
        $productRepository = $this->getProductRepository($request);
        $user = $this->getUserDueToQueryParamAccount($request, 'account');
        $params = array_merge($request->request->all(), $request->query->all());
        $request->query->add($request->request->all());
        $pagerParams = $this->_createPagerParams($params);

        $qb = $productRepository->getProductsForUserQuery($user, $pagerParams);

        if (!isset($params['page'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
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
     * @return mixed
     */
    public function getCommentsAction($id, Request $request)
    {
        $product = $this->get('biddy.domain_manager.product')->find($id);
        if (!$product instanceof ProductInterface || $product->getCommentVisibility() == ProductInterface::VISIBILITY_PRIVATE) {
            return [
                'count' => 0,
                'next' => null,
                'previous' => null,
                'results' => []
            ];
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
     * Get a single product group for the given id
     *
     * @Rest\View(serializerGroups={"product.detail", "user.summary", "reaction.minimum"})
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
     * @return mixed
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getShowsAction($id)
    {
        $product = $this->get('biddy.domain_manager.product')->find($id);
        if (!$product instanceof ProductInterface) {
            return [];
        }
        
        $group = $this->serializeProduct($product);

        //Reaction info
        /** @var ReactionManagerInterface $reactionManager */
        $reactionManager = $this->get('biddy.domain_manager.reaction');
        $group['reactions']['total'] = $reactionManager->findTotalReactionCountByProduct($product);
        $group['reactions']['emotion'] = $reactionManager->findTotalReactionCountByProductGroupByEmotion($product);
        $group['reactions']['userReaction'] = $reactionManager->getCurrentReactionByUser($this->getUser(), 'product', $product);

        return $group;
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
     * @return ProductInterface[]|PublicSimpleException
     */
    public function cgetRatingSummaryAction($id)
    {
        $product = $this->get('biddy.domain_manager.product')->find($id);
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
     * @return ProductInterface[]|PublicSimpleException
     */
    public function cgetProductRatingAction($id, Request $request)
    {
        $product = $this->get('biddy.domain_manager.product')->find($id);
        if (!$product instanceof ProductInterface) {
            return new PublicSimpleException("Object not exist");
        }

        $productRatingRepository = $this->get('biddy.repository.product_rating');
        $qb = $productRatingRepository->getProductRatingForProductQuery($product, $this->getParams());

        $params = array_merge($request->query->all(), $request->attributes->all());
        if (!isset($params['page']) && !isset($params['sortField']) &&
            !isset($params['orderBy']) && !isset($params['searchKey'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get all product auctions
     *
     * @Rest\View(serializerGroups={"auction.detail", "user.summary", "product.minimum"})
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
     *  section = "Product",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $id
     * @param Request $request
     * @return ProductInterface[]|PublicSimpleException
     */
    public function cgetAuctionsAction($id, Request $request)
    {
        $product = $this->get('biddy.domain_manager.product')->find($id);
        if (!$product instanceof ProductInterface) {
            return new PublicSimpleException("Object not exist");
        }

        $auctionRepository = $this->get('biddy.repository.auction');
        $qb = $auctionRepository->getAuctionForProductQuery($product, $this->getParams());

        $params = array_merge($request->query->all(), $request->attributes->all());
        if (!isset($params['page']) && !isset($params['sortField']) &&
            !isset($params['orderBy']) && !isset($params['searchKey'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get active product auctions
     *
     * @Rest\View(serializerGroups={"auction.detail", "user.summary"})
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
     *  section = "Product",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $id
     * @return ProductInterface[]|PublicSimpleException
     * @throws PublicSimpleException
     */
    public function cgetAuctionsActiveAction($id)
    {
        $product = $this->get('biddy.domain_manager.product')->find($id);
        if (!$product instanceof ProductInterface) {
            return new PublicSimpleException("Object not exist");
        }

        $auctionRepository = $this->get('biddy.repository.auction');

        return $auctionRepository->getActiveAuctionForProduct($product, date_create('now'));
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
     * @param $request
     * @param $param
     * @return AccountInterface|\Biddy\Model\User\UserEntityInterface|bool|\FOS\UserBundle\Model\UserInterface|mixed|null
     */
    private function getUserDueToQueryParamAccount($request, $param)
    {
        $params = array_merge($request->request->all(), $request->query->all());
        $accountId = isset($params[$param]) ? $params[$param] : null;

        if (empty($accountId)) {
            return $this->get('security.helper')->getToken()->getUser();
        }

        /** @var AccountManagerInterface $accountManager */
        $accountManager = $this->get('biddy_user.domain_manager.account');
        $account = $accountManager->findAccount($accountId);

        if ($account instanceof AccountInterface) {
            return $account;
        }

        /** @var SaleManagerInterface $saleManager */
        $saleManager = $this->get('biddy_user.domain_manager.sale');
        $sale = $saleManager->find($accountId);

        if ($sale instanceof SaleInterface) {
            return $sale;
        }

        /** @var AdminManagerInterface $adminManager */
        $adminManager = $this->get('biddy_user.domain_manager.admin');
        $admin = $adminManager->find($accountId);

        if ($admin instanceof AdminInterface) {
            return $admin;
        }

        throw new NotFoundHttpException('Not found account id #' . $accountId);
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