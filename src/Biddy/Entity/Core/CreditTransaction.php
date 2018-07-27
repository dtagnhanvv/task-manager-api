<?php

namespace Biddy\Entity\Core;


use Biddy\Model\Core\CreditTransaction as CreditTransactionModel;
use Biddy\Model\Core\WalletInterface;
use Biddy\Model\User\UserEntityInterface;

class CreditTransaction extends CreditTransactionModel
{
    protected $id;
    protected $deletedAt;
    protected $createdDate;

    protected $amount;
    protected $type;
    protected $detail;
    protected $targetWalletCreditBeforeTransaction;
    protected $fromWalletCreditBeforeTransaction;

    /** @var WalletInterface */
    protected $fromWallet;

    /** @var WalletInterface */
    protected $targetWallet;

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