<?php

namespace Biddy\Bundle\ApiBundle\Controller;

use Biddy\Model\Core\ProductRatingInterface;
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
 * @Rest\RouteResource("ProductRating")
 */
class ProductRatingController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;

    /**
     * Get all product ratings
     *
     * @Rest\View(serializerGroups={"product_rating.detail", "product.detail", "user.summary"})
     *
     * @Rest\QueryParam(name="account", nullable=true, requirements="\d+", description="the account id")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="ratingBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="types", nullable=true, description="the type to get")
     *
     * @ApiDoc(
     *  section = "ProductRating",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\ProductRatingInterface[]
     */
    public function cgetAction(Request $request)
    {
        $user = $this->getUserDueToQueryParamAccount($request, 'account');

        $productRatingRepository = $this->get('biddy.repository.product_rating');
        $qb = $productRatingRepository->getProductRatingsForUserQuery($user, null, null, $this->getParams());

        $params = array_merge($request->query->all(), $request->attributes->all());
        if (!isset($params['page']) && !isset($params['sortField']) && !isset($params['ratingBy']) && !isset($params['searchKey'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get all product ratings
     * @Rest\Get("/productratings/list")
     * @Rest\View(serializerGroups={"product_rating.detail", "product.detail", "user.summary"})
     *
     * @Rest\QueryParam(name="account", nullable=true, requirements="\d+", description="the account id")
     * @Rest\QueryParam(name="product", nullable=true, requirements="\d+", description="product id")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="ratingBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="types", nullable=true, description="the type to get")
     *
     * @ApiDoc(
     *  section = "ProductRating",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\ProductRatingInterface[]
     */
    public function cgetProductRatingsAction(Request $request)
    {
        $productRatingRepository = $this->get('biddy.repository.product_rating');
        $user = $this->getUserDueToQueryParamAccount($request, 'account');
        $product = $this->getProductDueToQueryParamProduct($request, 'product');
        $bill = $this->getBillDueToQueryParamProduct($request, 'bill');
        $qb = $productRatingRepository->getProductRatingsForUserQuery($user, $product, $bill, $this->getParams());

        $params = array_merge($request->request->all(), $request->query->all());
        if (!isset($params['page'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get a single product rating group for the given id
     *
     * @Rest\View(serializerGroups={"product_rating.detail", "product.detail", "user.summary"})
     *
     * @ApiDoc(
     *  section = "ProductRating",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return ProductRatingInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a product rating from the submitted data
     *
     * @ApiDoc(
     *  section = "ProductRating",
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
        $user = $this->getUserDueToQueryParamAccount($request, 'account');
        $product = $this->getProductDueToQueryParamProduct($request, 'product');
        $bill = $this->getBillDueToQueryParamProduct($request, 'bill');

        $productRatingRepository = $this->get('biddy.repository.product_rating');
        $qb = $productRatingRepository->getProductRatingsForUserQuery($user, $product, $bill, $this->getParams());
        $allRatings = $qb->getQuery()->getResult();

        if (empty($allRatings)) {
            //Creating new rating
            return $this->post($request);
        }

        //Overwrite current rating
        $currentRating = reset($allRatings);
        if ($currentRating instanceof ProductRatingInterface) {
            return $this->patchAction($request, $currentRating->getId());
        }
    }

    /**
     * Update an existing product rating from the submitted data or create a new ad network
     *
     * @ApiDoc(
     *  section = "ProductRating",
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
     * Update an existing product rating from the submitted data or create a new product rating at a specific location
     *
     * @ApiDoc(
     *  section = "ProductRating",
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
     * Delete an existing product rating
     *
     * @ApiDoc(
     *  section = "ProductRating",
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
        return 'productrating';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_productrating';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_api.handler.product_rating');
    }

    /**
     * @param Request $request
     * @param $productKey
     * @return \Biddy\Model\ModelInterface|null|object
     */
    private function getProductDueToQueryParamProduct(Request $request, $productKey)
    {
        $params = array_merge($request->request->all(), $request->query->all());
        if (isset($params[$productKey])) {
            $productManager = $this->get('biddy.domain_manager.product');

            return $productManager->find($params[$productKey]);
        }

        return null;
    }
    
    /**
     * @param Request $request
     * @param $billKey
     * @return \Biddy\Model\ModelInterface|null|object
     */
    private function getBillDueToQueryParamProduct(Request $request, $billKey)
    {
        $params = array_merge($request->request->all(), $request->query->all());
        if (isset($params[$billKey])) {
            $billManager = $this->get('biddy.domain_manager.bill');

            return $billManager->find($params[$billKey]);
        }

        return null;
    }
}