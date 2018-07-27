<?php

namespace Biddy\Bundle\AdminApiBundle\Controller;

use Biddy\Service\Util\SaleUtilTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Biddy\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Biddy\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Biddy\Model\User\Role\AccountInterface;

class SaleController extends RestControllerAbstract implements ClassResourceInterface
{
    use SaleUtilTrait;

    /**
     * Get all sale
     * @Rest\View(serializerGroups={"sale.detail", "user.summary"})
     * @Rest\Get("/sales")
     * @Rest\QueryParam(name="all", requirements="(true|false)", nullable=true)
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return \Biddy\Bundle\UserSystem\SaleBundle\Entity\User[]
     */
    public function cgetAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $all = $paramFetcher->get('all');

        if ($all === null || !filter_var($all, FILTER_VALIDATE_BOOLEAN)) {
            return $this->getHandler()->allActiveAccounts();
        }

        return $this->getHandler()->allAccounts();
    }

    /**
     * Get a single sale for the given id
     * @Rest\View(serializerGroups={"sale.detail", "sale.billing", "user.summary"})
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Biddy\Bundle\UserSystem\SaleBundle\Entity\User
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get token for sale only
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $saleId
     * @return array
     */
    public function getTokenAction($saleId)
    {
        $saleManager = $this->get('biddy_sale.domain_manager.sale');

        /** @var AccountInterface $sale */
        $sale = $saleManager->findAccount($saleId);

        if (!$sale) {
            throw new NotFoundHttpException('That sale does not exist');
        }

        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');
        $jwtTransformer = $this->get('biddy_api.service.jwt_response_transformer');

        $tokenString = $jwtManager->create($sale);

        return $jwtTransformer->transform(['token' => $tokenString], $sale);
    }

    /**
     * Create a sale from the submitted data
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  parameters={
     *      {"name"="salename", "dataType"="string", "required"=true},
     *      {"name"="email", "dataType"="string", "required"=false},
     *      {"name"="plainPassword", "dataType"="string", "required"=true},
     *      {"name"="role", "dataType"="string", "required"=true, "default"="sale", "description"="The role of the sale, i.e sale or admin"},
     *      {"name"="features", "dataType"="array", "required"=false, "description"="An array of enabled features for this sale, not applicable to admins"},
     *      {"name"="enabled", "dataType"="boolean", "required"=false, "description"="Is this sale account enabled or not?"},
     *  },
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
     * Get list sales pagination
     * @Rest\Post("/sales/list")
     *
     * @ApiDoc(
     *  section = "Users",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\UserInterface[]
     */
    public function postSalesAction(Request $request)
    {
        $params = array_merge($request->query->all(), $request->request->all());
        $request->query->add($request->request->all());
        $pagerParams = $this->_createParams($params);
        if (isset($params['limit']) && isset($params['page'])) {
            return $this->getPagination($this->getUserQuery($this->get('doctrine.orm.entity_manager'), $pagerParams), $request);
        }

        return $this->get('biddy_admin_api.repository.sale')->findAll();
    }

    /**
     * Update an existing sale from the submitted data or create a new sale
     *
     * @ApiDoc(
     *  section = "admin",
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
     * Update an existing sale from the submitted data or create a new sale at a specific location
     *
     * @ApiDoc(
     *  section = "admin",
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
     * Delete an existing sale
     *
     * @ApiDoc(
     *  section = "admin",
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
     * @inheritdoc
     */
    protected function getResourceName()
    {
        return 'sale';
    }

    /**
     * @inheritdoc
     */
    protected function getGETRouteName()
    {
        return 'admin_api_1_get_sale';
    }

    /**
     * @return UserHandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_admin_api.handler.sale');
    }
}