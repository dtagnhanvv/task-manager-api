<?php

namespace Biddy\Bundle\UserSystem\SaleBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Biddy\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Biddy\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Biddy\Exception\LogicException;
use Biddy\Model\User\Role\SaleInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("sales/current")
 */
class SaleController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get current sale
     * @Rest\View(
     *      serializerGroups={"user.detail"}
     * )
     * @return \Biddy\Bundle\UserBundle\Entity\User
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction()
    {
        $saleId = $this->get('security.helper')->getToken()->getUser()->getId();

        return $this->one($saleId);
    }

    /**
     * Update current sale from the submitted data
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when resource not exist
     */
    public function patchAction(Request $request)
    {
        $saleId = $this->get('security.helper')->getToken()->getUser()->getId();

        return $this->patch($request, $saleId);
    }

    /**
     * get account as Sale by saleId
     * @Rest\View(
     *      serializerGroups={"user.detail"}
     * )
     * @param integer $saleId
     * @return SaleInterface Sale
     */
    protected function getSale($saleId)
    {
        try {
            $sale = $this->one($saleId);
        } catch (\Exception $e) {
            $sale = false;
        }

        if (!$sale instanceof SaleInterface) {
            throw new LogicException('The user should have the sale role');
        }

        return $sale;
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
        return 'sale_api_1_get_current';
    }

    /**
     * @return UserHandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_admin_api.handler.sale');
    }
}