<?php

namespace Biddy\Worker\Job\Concurrent\Logger;

use Biddy\Service\Auditing\CreditTransaction\CreditTransactionLoggerInterface;
use Biddy\Worker\Core\Job\JobInterface;
use Biddy\Worker\Core\JobParams;
use Monolog\Logger;

class CreditTransactionLoggerWorker implements JobInterface
{
    const JOB_NAME = 'CreditTransactionLoggerWorker';
    const PARAM_KEY_CREDIT_TRANSACTION_IDS = 'credit_transaction_ids';

    /** @var Logger $logger */
    private $logger;

    /** @var CreditTransactionLoggerInterface */
    private $creditTransactionLogger;

    /**
     * CreditTransactionLoggerWorker constructor.
     * @param Logger $logger
     * @param CreditTransactionLoggerInterface $creditTransactionLogger
     */
    public function __construct(Logger $logger, CreditTransactionLoggerInterface $creditTransactionLogger)
    {
        $this->logger = $logger;
        $this->creditTransactionLogger = $creditTransactionLogger;
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
        $creditTransactionIds = $params->getRequiredParam(self::PARAM_KEY_CREDIT_TRANSACTION_IDS);

        if (empty($creditTransactionIds)) {
            return;
        }

        $this->creditTransactionLogger->logFileForCreditTransactions($creditTransactionIds);
    }
}