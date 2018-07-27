<?php

namespace Biddy\Worker\Job\Concurrent\Credit;

use Biddy\DomainManager\BillManagerInterface;
use Biddy\Model\Core\BillInterface;
use Biddy\Service\Bidding\Core\BidCreditManagerInterface;
use Biddy\Worker\Core\Job\JobInterface;
use Biddy\Worker\Core\JobParams;
use Monolog\Logger;

class TransferCreditAfterConfirmBillWorker implements JobInterface
{
    const JOB_NAME = 'TransferCreditAfterConfirmBillWorker';
    const PARAM_KEY_BILL_IDS = 'bill_ids';
    const PARAM_KEY_CONTEXT = 'context';

    /** @var Logger $logger */
    private $logger;

    /** @var BidCreditManagerInterface */
    private $bidCreditManager;

    /** @var BillManagerInterface */
    private $billManager;

    /**
     * CreditTransactionLoggerWorker constructor.
     * @param Logger $logger
     * @param BidCreditManagerInterface $bidCreditManager
     * @param BillManagerInterface $billManager
     */
    public function __construct(Logger $logger, BidCreditManagerInterface $bidCreditManager, BillManagerInterface $billManager)
    {
        $this->logger = $logger;
        $this->bidCreditManager = $bidCreditManager;
        $this->billManager = $billManager;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::JOB_NAME;
    }

    /**
     * @inheritdoc
     */
    public function run(JobParams $params)
    {
        $billIds = $params->getRequiredParam(self::PARAM_KEY_BILL_IDS);

        if (empty($billIds)) {
            return;
        }

        foreach ($billIds as $id) {
            $bill = $this->billManager->find($id);
            if (!$bill instanceof BillInterface) {
                continue;
            }

            $this->bidCreditManager->transferCreditTransaction($bill);
        }
    }
}