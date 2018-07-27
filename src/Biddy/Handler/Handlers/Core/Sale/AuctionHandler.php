<?php

namespace Biddy\Handler\Handlers\Core\Sale;


use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\Handler\Handlers\Core\AuctionHandlerAbstract;
use Biddy\Model\User\Role\SaleInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Symfony\Component\Form\FormFactoryInterface;

class AuctionHandler extends AuctionHandlerAbstract
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
        return $role instanceof SaleInterface;
    }
}