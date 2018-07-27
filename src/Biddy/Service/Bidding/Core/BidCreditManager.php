<?php

namespace Biddy\Service\Bidding\Core;

use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\DomainManager\CreditTransactionManagerInterface;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BillInterface;
use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Service\CreditTransaction\CreditTransactionFactoryInterface;

class BidCreditManager implements BidCreditManagerInterface
{
    /** @var  AccountManagerInterface */
    private $accountManager;

    /** @var  CreditTransactionFactoryInterface */
    private $creditTransactionFactory;

    /** @var  CreditTransactionManagerInterface */
    private $creditTransactionManager;

    /**
     * BidCreditManager constructor.
     * @param AccountManagerInterface $accountManager
     * @param CreditTransactionFactoryInterface $creditTransactionFactory
     * @param CreditTransactionManagerInterface $creditTransactionManager
     */
    public function __construct(AccountManagerInterface $accountManager, CreditTransactionFactoryInterface $creditTransactionFactory, CreditTransactionManagerInterface $creditTransactionManager)
    {
        $this->accountManager = $accountManager;
        $this->creditTransactionFactory = $creditTransactionFactory;
        $this->creditTransactionManager = $creditTransactionManager;
    }

    /**
     * @inheritdoc
     */
    public function transferCreditTransaction(BillInterface $bill)
    {
        $creditTransaction = $this->creditTransactionFactory->transferCreditTransaction($bill);
        if (!$creditTransaction instanceof CreditTransactionInterface) {
            return;
        }

        $this->creditTransactionManager->save($creditTransaction);
    }

    /**
     * @inheritdoc
     */
    public function payFeeForNewAuction(AuctionInterface $auction)
    {
        $creditTransaction = $this->creditTransactionFactory->payFeeToNewAuction($auction);
        if ($creditTransaction instanceof CreditTransactionInterface) {
            $this->creditTransactionManager->save($creditTransaction);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function payFeeForBill(BillInterface $bill)
    {
        $creditTransaction = $this->creditTransactionFactory->payFeeToFeeWallet($bill);
        if ($creditTransaction instanceof CreditTransactionInterface) {
            $this->creditTransactionManager->save($creditTransaction);
        }
    }

    /**
     * @inheritdoc
     */
    public function returnFeeForRejectedBill(BillInterface $bill)
    {
        $creditTransaction = $this->creditTransactionFactory->returnFeeToBasicWallet($bill);
        if ($creditTransaction instanceof CreditTransactionInterface) {
            $this->creditTransactionManager->save($creditTransaction);
        }
    }
}