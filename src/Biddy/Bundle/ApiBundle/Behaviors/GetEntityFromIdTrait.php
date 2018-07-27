<?php

namespace Biddy\Bundle\ApiBundle\Behaviors;


use Biddy\Bundle\UserBundle\DomainManager\AdminManagerInterface;
use Biddy\Bundle\UserBundle\DomainManager\SaleManagerInterface;
use Biddy\Model\User\Role\SaleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\DomainManager\ManagerInterface;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\UserEntityInterface;

trait GetEntityFromIdTrait
{
    /**
     * get User Due To Param Account
     *
     * if current user is Account => not check param
     *
     * if current user is Admin => check param and return found account
     *
     * @param Request $request
     * @param string $param default is 'account'
     * @return AdminInterface|AccountInterface
     * @throws NotFoundHttpException if user is admin and account is not found
     */
    public function getUserDueToQueryParamAccount(Request $request, $param = 'account')
    {
//        $currentUser = $this->getUser();
//
//        // notice: param "account" is only used if current account is admin or admin
//        if ($currentUser instanceof AccountInterface) {
//            return $currentUser;
//        }

        $accountId = $request->query->get($param, null);
        if (empty($accountId)) {
            return $this->get('security.helper')->getToken()->getUser();
        }
        
        /** @var AccountManagerInterface $accountManager */
        $accountManager = $this->getService('biddy_user.domain_manager.account');
        $account = $accountManager->findAccount($accountId);

        if ($account instanceof AccountInterface) {
            return $account;
        }

        /** @var SaleManagerInterface $saleManager */
        $saleManager = $this->getService('biddy_user.domain_manager.sale');
        $sale = $saleManager->find($accountId);

        if ($sale instanceof SaleInterface) {
            return $sale;
        }

        /** @var AdminManagerInterface $adminManager */
        $adminManager = $this->getService('biddy_user.domain_manager.admin');
        $admin = $adminManager->find($accountId);

        if ($admin instanceof AdminInterface) {
            return $admin;
        }

        throw new NotFoundHttpException('Not found account id #' . $accountId);
    }

    /**
     * create entity objects from manager and ids with expected class
     *
     * @param ManagerInterface $manager
     * @param array $ids
     * @param string $class
     * @return array|ModelInterface[]
     */
    private function createEntitiesObject(ManagerInterface $manager, array $ids, $class)
    {
        $entities = [];

        foreach ($ids as $id) {
            $entity = $manager->find($id);

            if (!is_a($entity, $class)) {
                throw new NotFoundHttpException(sprintf('entity %s with id %d is not found', $class, $id));
            }

            if (!in_array($entity, $entities)) {
                $this->checkUserPermission($entity, 'edit');
                $entities[] = $entity;
            }
        }

        return $entities;
    }

    /**
     * convert an input to array
     *
     * @param mixed $ids one or array
     * @return array
     */
    private function convertInputToArray($ids)
    {
        if (is_numeric($ids) && $ids < 1) {
            throw new InvalidArgumentException('Expect a positive integer or array');
        }

        return !is_array($ids) ? [$ids] : $ids;
    }

    /**
     * check user permission for an entity
     *
     * @param ModelInterface $entity
     * @param string $permission
     * @return mixed
     */
    protected abstract function checkUserPermission($entity, $permission = 'view');

    /**
     * Get service instance, this should be called in a controller or a container-aware service which has container to get a service by id
     *
     * @param $id
     * @return object
     */
    private function getService($id)
    {
        return $this->get($id);
    }

    /**
     * get current user
     * @return UserEntityInterface
     */
    public abstract function getUser();
}