<?php

namespace Biddy\Handler\Handlers\Core\Admin;


use Biddy\Handler\Handlers\Core\CreditTransactionHandlerAbstract;
use Symfony\Component\Form\FormFactoryInterface;
use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\UserRoleInterface;

class CreditTransactionHandler extends CreditTransactionHandlerAbstract
{
    /** @var AccountManagerInterface */
    private $accountManager;

    /**
     * @param FormFactoryInterface $formFactory
     * @param string $formType
     * @param $domainManager
     * @param AccountManagerInterface $accountManager
     */
    function __construct(FormFactoryInterface $formFactory, $formType, $domainManager, AccountManagerInterface $accountManager)
    {
        parent:: __construct($formFactory, $formType, $domainManager, $userRole = null);

        $this->accountManager = $accountManager;
    }

    /**
     * @inheritdoc
     */
    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof AdminInterface;
    }
}