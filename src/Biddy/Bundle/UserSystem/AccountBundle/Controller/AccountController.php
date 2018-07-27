<?php

namespace Biddy\Bundle\UserSystem\AccountBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Biddy\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Biddy\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Biddy\Exception\LogicException;
use Biddy\Model\User\Role\AccountInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("accounts/current")
 */
class AccountController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get current account
     * @Rest\View(
     *      serializerGroups={"user.detail"}
     * )
     * @return \Biddy\Bundle\UserBundle\Entity\User
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction()
    {
        $accountId = $this->get('security.helper')->getToken()->getUser()->getId();

        return $this->one($accountId);
    }

    /**
     * Update current account from the submitted data
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when resource not exist
     */
    public function patchAction(Request $request)
    {
        $accountId = $this->get('security.helper')->getToken()->getUser()->getId();

        return $this->patch($request, $accountId);
    }

    /**
     * get account as Account by accountId
     * @Rest\View(
     *      serializerGroups={"user.detail"}
     * )
     * @param integer $accountId
     * @return AccountInterface Account
     */
    protected function getAccount($accountId)
    {
        try {
            $account = $this->one($accountId);
        } catch (\Exception $e) {
            $account = false;
        }

        if (!$account instanceof AccountInterface) {
            throw new LogicException('The user should have the account role');
        }

        return $account;
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
        return 'account_api_1_get_current';
    }

    /**
     * @return UserHandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_admin_api.handler.user');
    }
}
