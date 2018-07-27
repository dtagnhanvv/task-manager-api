<?php

namespace Biddy\Handler\Handlers\Core\Account;


use Biddy\Exception\LogicException;
use Biddy\Handler\Handlers\Core\AlertHandlerAbstract;
use Biddy\Model\Core\AlertInterface;
use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;

class AlertHandler extends AlertHandlerAbstract
{
    /**
     * @inheritdoc
     */
    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof AccountInterface;
    }

    /**
     * @inheritdoc
     * @return AccountInterface
     * @throws LogicException
     */
    public function getUserRole()
    {
        $role = parent::getUserRole();

        if (!$role instanceof AccountInterface) {
            throw new LogicException('userRole does not implement AccountInterface');
        }

        return $role;
    }

    public function all($limit = null, $offset = null)
    {
        return $this->getDomainManager()->all($limit, $offset);
    }

    /**
     * @inheritdoc
     */
    protected function processForm(ModelInterface $alert, array $parameters, $method = "PUT")
    {
        /** @var AlertInterface $alert */
        if (null == $alert->getAccount()) {
            $alert->setAccount($this->getUserRole());
        }

        return parent::processForm($alert, $parameters, $method);
    }
}