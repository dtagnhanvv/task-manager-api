<?php

namespace Biddy\Bundle\ApiBundle\Controller;

use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Service\Util\CreditTransactionUtilTrait;
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
 * @Rest\RouteResource("CreditTransaction")
 */
class CreditTransactionController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;
    use CreditTransactionUtilTrait;

    /**
     * Get all credit transactions
     *
     * @Rest\View(serializerGroups={"credit_transaction.detail", "product.detail", "user.detail", "sale.detail", "admin.detail"})
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
     *  section = "CreditTransaction",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\CreditTransactionInterface[]
     */
    public function cgetAction(Request $request)
    {
        $user = $this->getUserDueToQueryParamAccount($request, 'account');

        $creditTransactionRepository = $this->get('biddy.repository.credit_transaction');
        $qb = $creditTransactionRepository->getCreditTransactionsForUserQuery($user, $this->getParams());

        $params = array_merge($request->query->all(), $request->attributes->all());
        if (!isset($params['page']) && !isset($params['sortField']) && !isset($params['ratingBy']) && !isset($params['searchKey'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get all credit transactions
     * @Rest\Post("/credittransactions/list")
     * @Rest\View(serializerGroups={"credit_transaction.detail", "product.detail", "user.summary", "admin.summary", "sale.summary", "wallet.summary"})
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
     *  section = "CreditTransaction",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\CreditTransactionInterface[]
     */
    public function postCreditTransactionsAction(Request $request)
    {
        $user = $this->getUserDueToQueryParamAccount($request, 'account');

        $params = array_merge($request->query->all(), $request->request->all());
        $request->query->add($params);

        $creditTransactionRepository = $this->get('biddy.repository.credit_transaction');
        $qb = $creditTransactionRepository->getCreditTransactionsForUserQuery($user, $this->_createParams($params));

        $pagination = $this->getPagination($qb, $request);
        $pagination['records'] = $this->serializeCreditTransactions($pagination['records']);

        return $pagination;
    }

    /**
     * Get a single credit transaction group for the given id
     *
     * @Rest\View(serializerGroups={"credit_transaction.detail", "product.detail", "user.summary"})
     *
     * @ApiDoc(
     *  section = "CreditTransaction",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return CreditTransactionInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a credit transaction from the submitted data
     *
     * @ApiDoc(
     *  section = "CreditTransaction",
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
     * Update an existing credit transaction from the submitted data or create a new ad network
     *
     * @ApiDoc(
     *  section = "CreditTransaction",
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
     * Update an existing credit transaction from the submitted data or create a new credit transaction at a specific location
     *
     * @ApiDoc(
     *  section = "CreditTransaction",
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
     * Delete an existing credit transaction
     *
     * @ApiDoc(
     *  section = "CreditTransaction",
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
        return 'credittransaction';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_credittransaction';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_api.handler.credit_transaction');
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
}