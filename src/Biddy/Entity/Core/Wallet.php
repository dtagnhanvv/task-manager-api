<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\Core\Wallet as WalletModel;
use Biddy\Model\User\Role\UserRoleInterface;

class Wallet extends WalletModel
{
    protected $id;
    protected $createdDate;
    protected $deletedAt;

    protected $type;
    protected $name;
    protected $currency;
    protected $validFrom;
    protected $expiredAt;
    protected $credit;
    protected $previousCredit;

    /**
     * @var UserRoleInterface
     */
    protected $owner;

    /** @var CreditTransactionInterface[] */
    protected $creditTransactions;
    
    /**
     * @inheritdoc
     *
     * inherit constructor for inheriting all default initialized value
     */
    public function __construct()
    {
        parent::__construct();
    }
}