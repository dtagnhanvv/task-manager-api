<?php

namespace Biddy\Handler\Handlers\Core\Account;


use Biddy\Exception\LogicException;
use Biddy\Handler\Handlers\Core\TaskHandlerAbstract;
use Biddy\Model\Core\TaskInterface;
use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;

class TaskHandler extends TaskHandlerAbstract
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
    protected function processForm(ModelInterface $task, array $parameters, $method = "PUT")
    {
        /** @var TaskInterface $task */
        if (null == $task->getOwner()) {
            $task->setOwner($this->getUserRole());
        }

        return parent::processForm($task, $parameters, $method);
    }
}