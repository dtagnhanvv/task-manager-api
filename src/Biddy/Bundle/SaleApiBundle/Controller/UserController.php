<?php

namespace Biddy\Bundle\SaleApiBundle\Controller;

use Biddy\Service\Util\UserUtilTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Biddy\Bundle\SaleApiBundle\Handler\UserHandlerInterface;
use Biddy\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\Model\User\Role\AccountInterface;

class UserController extends RestControllerAbstract implements ClassResourceInterface
{
    use UserUtilTrait;

    /**
     * Get all account
     * @Rest\View(serializerGroups={"user.detail","user.billing"})
     * @Rest\Get("/users")
     * @Rest\QueryParam(name="all", requirements="(true|false)", nullable=true)
     * @ApiDoc(
     *  section = "sale",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return \Biddy\Bundle\UserBundle\Entity\User[]
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
     * Get a single account for the given id
     * @Rest\View(serializerGroups={"user.detail", "user.billing"})
     * @ApiDoc(
     *  section = "sale",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Biddy\Bundle\UserBundle\Entity\User
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        if ($id == 'current') {
            return $this->get('security.helper')->getToken()->getUser();
        }
        
        return $this->one($id);
    }

    /**
     * Get token for account only
     *
     * @ApiDoc(
     *  section = "sale",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $accountId
     * @return array
     */
    public function getTokenAction($accountId)
    {
        /** @var AccountManagerInterface $accountManager */
        $accountManager = $this->get('biddy_user.domain_manager.account');

        /** @var AccountInterface $account */
        $account = $accountManager->findAccount($accountId);

        if (!$account) {
            throw new NotFoundHttpException('That account does not exist');
        }

        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');
        $jwtTransformer = $this->get('biddy_api.service.jwt_response_transformer');

        $tokenString = $jwtManager->create($account);

        return $jwtTransformer->transform(['token' => $tokenString], $account);
    }

    /**
     * Create a user from the submitted data
     *
     * @ApiDoc(
     *  section = "sale",
     *  resource = true,
     *  parameters={
     *      {"name"="username", "dataType"="string", "required"=true},
     *      {"name"="email", "dataType"="string", "required"=false},
     *      {"name"="plainPassword", "dataType"="string", "required"=true},
     *      {"name"="role", "dataType"="string", "required"=true, "default"="account", "description"="The role of the user, i.e account or sale"},
     *      {"name"="features", "dataType"="array", "required"=false, "description"="An array of enabled features for this user, not applicable to sales"},
     *      {"name"="enabled", "dataType"="boolean", "required"=false, "description"="Is this user account enabled or not?"},
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
     * Get list users pagination
     * @Rest\Post("/users/list")
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
    public function postUsersAction(Request $request)
    {
        $params = array_merge($request->query->all(), $request->request->all());
        $request->query->add($request->request->all());
        $pagerParams = $this->_createParams($params);
        if (isset($params['limit']) && isset($params['page'])) {
            return $this->getPagination($this->getUserQuery($this->get('doctrine.orm.entity_manager'), $pagerParams), $request);
        }

        return $this->get('biddy_sale_api.repository.user')->findAll();
    }

    /**
     * Update an existing user from the submitted data or create a new account
     *
     * @ApiDoc(
     *  section = "sale",
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
     * Update an existing user from the submitted data or create a new account at a specific location
     *
     * @ApiDoc(
     *  section = "sale",
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
     * Delete an existing account
     *
     * @ApiDoc(
     *  section = "sale",
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
        return 'user';
    }

    /**
     * @inheritdoc
     */
    protected function getGETRouteName()
    {
        return 'sale_api_1_get_user';
    }

    /**
     * @return UserHandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_sale_api.handler.user');
    }
}
