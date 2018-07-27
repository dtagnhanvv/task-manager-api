<?php

namespace Biddy\Bundle\ApiBundle\Controller;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Service\Util\AccountUtilTrait;
use Biddy\Service\Util\AuctionUtilTrait;
use Biddy\Service\Bidding\Core\AuctionRuleInterface;
use Biddy\Service\Util\CommentUtilTrait;
use Biddy\Service\Util\ProductUtilTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Biddy\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Biddy\Handler\HandlerInterface;

/**
 * @Rest\RouteResource("Auction")
 */
class AuctionController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;
    use ProductUtilTrait;
    use CommentUtilTrait;
    use AccountUtilTrait;
    use AuctionUtilTrait;

    /**
     * Get all auctions
     *
     * @Rest\View(serializerGroups={"auction.detail", "user.summary"})
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
     *  section = "Auction",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\AuctionInterface[]
     */
    public function cgetAction(Request $request)
    {
        $user = $this->getUserDueToQueryParamAccount($request, 'account');

        $auctionRepository = $this->get('biddy.repository.auction');
        $qb = $auctionRepository->getAuctionsForUserQuery($user, $this->getParams());

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
     * Get all auctions
     * @Rest\Get("/bidauctions/list")
     * @Rest\View(serializerGroups={"auction.detail", "user.summary"})
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
     *  section = "Auction",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\AuctionInterface[]
     */
    public function cgetAuctionsAction(Request $request)
    {
        $auctionRepository = $this->get('biddy.repository.auction');
        $user = $this->getUserDueToQueryParamAccount($request, 'account');
        $params = array_merge($request->request->all(), $request->query->all());
        $qb = $auctionRepository->getAuctionsForUserQuery($user, $this->getParams());

        if (!isset($params['page'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get a single auction group for the given id
     *
     * @Rest\View(serializerGroups={"auction.detail", "user.summary", "product.summary"})
     *
     * @ApiDoc(
     *  section = "Auction",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return AuctionInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get a next price of bid in an auction
     *
     * @Rest\View(serializerGroups={"auction.detail", "user.summary"})
     *
     * @ApiDoc(
     *  section = "Auction",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return integer
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getPriceNextAction($id)
    {
        /** @var AuctionInterface $auction */
        $auction = $this->one($id);

        switch ($auction->getType()) {
            case AuctionRuleInterface::MANUAL:
                return $auction->getMinimumPrice();
            case AuctionRuleInterface::AUTOMATED:
                switch ($auction->getObjective()) {
                    case AuctionInterface::OBJECTIVE_LOWEST_PRICE:
                        return $auction->getMinimumPrice();
                    case AuctionInterface::OBJECTIVE_HIGHEST_PRICE:
                        $bidRepository = $this->get('biddy.repository.bid');
                        return $this->calculateNextPrice($auction, $bidRepository);
                }
        }

        return 0;
    }


    /**
     * Create a auction from the submitted data
     *
     * @ApiDoc(
     *  section = "Auction",
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
     * Get product bids of a single product group for the given id
     *
     * @Rest\View(serializerGroups={"product.minimum", "bid.summary", "user.minimum"})
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
     * @return array|BidInterface
     */
    public function getBidsAction($id, Request $request)
    {
        $auction = $this->one($id);
        $user = $this->getUserDueToQueryParamAccount($request);

        if (!$auction instanceof AuctionInterface) {
            return [];
        }

        $params = array_merge($request->request->all(), $request->query->all());
        $request->query->add($request->request->all());
        $pagerParams = $this->_createPagerParams($params);

        $bidRepository = $this->get('biddy.repository.bid');
        $qb = $bidRepository->getBidsForAuctionQuery($auction, $pagerParams, $user);

        if (!isset($params['page']) && !isset($params['sortField']) &&
            !isset($params['orderBy']) && !isset($params['searchKey'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get bids for current account
     * @Rest\Post("/auctions/{id}/buyers")
     * @Rest\View(
     *      serializerGroups={"user.minimum"}
     * )
     * @param $id
     * @param Request $request
     * @return \Biddy\Bundle\UserBundle\Entity\User
     */
    public function postBidBuyerAction($id, Request $request)
    {
        /** @var AuctionInterface $auction */
        $auction = $this->one($id);
        $bidRepository = $this->get('biddy.repository.bid');
        $accountManager = $this->get('biddy_user.domain_manager.account');

        $params = array_merge($request->query->all(), $request->request->all());
        $request->query->add($params);

        $result = $bidRepository->getUserBiddingForProductQuery($auction, $this->_createPagerParams($params));
        $pagination = $this->getPagination(reset($result), $request);
        $pagination['records'] = $this->serializeBuyers(
            $pagination['records'], $auction, $bidRepository, $accountManager);
        $pagination['totalRecord'] = end($result);

        return $pagination;
    }

    /**
     * Manual close bidding for a product
     * @Rest\View(
     *      serializerGroups={"user.minimum"}
     * )
     * @param $id
     * @param Request $request
     * @return mixed
     * @throws \Biddy\Service\Util\PublicSimpleException
     */
    public function postCloseAction($id, Request $request)
    {
        /** @var AuctionInterface $auction */
        $auction = $this->one($id);
        $auctionManager = $this->get('biddy.service.bidding.core.auction_closer');
        $params = array_merge($request->query->all(), $request->request->all());

        return $auctionManager->closeAuction($auction, $params);
    }

    /**
     * Cancel auction without winner
     * @Rest\Post("/auctions/{id}/cancel")
     * @Rest\View(serializerGroups={"product.minimum", "bid.minimum", "user.minimum"})
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
    public function postCancelAction($id, Request $request)
    {
        $auction = $this->one($id);

        if (!$auction instanceof AuctionInterface) {
            return [];
        }

        $user = $this->getUserDueToQueryParamAccount($request);
        $bidManager = $this->get('biddy.domain_manager.bid');
        $bids = $auction->getBids();

        foreach ($bids as $bid) {
            if (!$bid instanceof BidInterface || $bid->getStatus() != BidInterface::STATUS_BIDDING) {
                continue;
            }

            if ($user instanceof UserRoleInterface && $user->getId() != $bid->getBuyer()->getId()) {
                continue;
            }
            
            $bid->setStatus(BidInterface::STATUS_CANCEL);

            $bidManager->save($bid);
        }

        return [];
    }

    /**
     * Get product bids of a single product group for the given id
     * @Rest\Post("/auctions/{id}/bids/cancel")
     * @Rest\View(serializerGroups={"product.minimum", "bid.minimum", "user.minimum"})
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
    public function postCancelBidsAction($id, Request $request)
    {
        $auction = $this->one($id);

        if (!$auction instanceof AuctionInterface) {
            return [];
        }

        $bidRepository = $this->get('biddy.repository.bid');
        $bidManager = $this->get('biddy.domain_manager.bid');

        $user = $this->getUserDueToQueryParamAccount($request, 'account');
        $qb = $bidRepository->getUserBidsForProductQuery($auction, $user);
        $bids = $qb->getQuery()->getResult();

        foreach ($bids as $bid) {
            if (!$bid instanceof BidInterface || $bid->getStatus() != BidInterface::STATUS_BIDDING) {
                continue;
            }

            $bid->setStatus(BidInterface::STATUS_CANCEL);

            $bidManager->save($bid);
        }

        return [];
    }

    /**
     * Update an existing auction from the submitted data or create a new ad network
     *
     * @ApiDoc(
     *  section = "Auction",
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
     * Update an existing auction from the submitted data or create a new auction at a specific location
     *
     * @ApiDoc(
     *  section = "Auction",
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
        return 'auction';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_auction';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_api.handler.auction');
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
}