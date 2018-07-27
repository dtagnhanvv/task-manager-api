<?php

namespace Biddy\DomainManager;

use Biddy\Model\User\Role\AccountInterface;

interface ProductManagerInterface extends ManagerInterface, MultiFormManagerInterface
{
    /**
     * @param AccountInterface $seller
     * @return mixed
     */
    public function getProductsForAccountQuery(AccountInterface $seller);

    /**
     * @param $model
     * @return mixed
     */
    public function getRepositoryByModel($model);
}