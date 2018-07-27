<?php

namespace Biddy\Worker;

use Biddy\Service\Util\DateUtilInterface;
use Biddy\Worker\Core\Scheduler\ConcurrentJobScheduler;
use Biddy\Worker\Core\Scheduler\ConcurrentJobSchedulerInterface;
use Biddy\Worker\Core\Scheduler\LinearJobScheduler;
use Biddy\Worker\Core\Scheduler\LinearJobSchedulerInterface;
use Biddy\Worker\Job\Concurrent\Alert\ProcessAlertWorker;
use Biddy\Worker\Job\Concurrent\Auction\DailyCloseAuctionsWorker;
use Biddy\Worker\Job\Concurrent\Credit\TransferCreditAfterConfirmBillWorker;
use Biddy\Worker\Job\Concurrent\Fee\PayFeeForBillWorker;
use Biddy\Worker\Job\Concurrent\Fee\PayFeeForNewAuctionWorker;
use Biddy\Worker\Job\Concurrent\Fee\ReturnFeeForRejectedBillWorker;
use Biddy\Worker\Job\Concurrent\Logger\CreditTransactionLoggerWorker;
use Biddy\Worker\Job\Concurrent\Rating\CalculateSellerRatingWorker;
use Biddy\Worker\Job\Concurrent\Wallet\CreateWalletsForUserWorker;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;
use Redis;

// responsible for creating background tasks

class Manager
{
    /** @var Redis */
    protected $redis;

    /** @var PheanstalkProxy */
    protected $beanstalk;

    /** @var LinearJobSchedulerInterface */
    protected $linearJobScheduler;

    /** @var ConcurrentJobSchedulerInterface */
    protected $concurrentJobScheduler;

    /** @var DateUtilInterface */
    private $dateUtil;

    public function __construct(DateUtilInterface $dateUtil, Redis $redis, PheanstalkProxy $beanstalk, ConcurrentJobScheduler $concurrentJobScheduler,
                                LinearJobScheduler $linearJobScheduler)
    {
        $this->dateUtil = $dateUtil;
        $this->redis = $redis;
        $this->beanstalk = $beanstalk;
        $this->concurrentJobScheduler = $concurrentJobScheduler;
        $this->linearJobScheduler = $linearJobScheduler;
    }

    /**
     * @param int $objectType
     * @param int $objectIds
     * @param array $action
     * @param $context
     */
    public function processAlert($objectType, $objectIds, $action, $context)
    {
        $jobData = [
            'task' => ProcessAlertWorker::JOB_NAME,
            ProcessAlertWorker::PARAM_KEY_OBJECT_TYPE => $objectType,
            ProcessAlertWorker::PARAM_KEY_OBJECT_IDS => $objectIds,
            ProcessAlertWorker::PARAM_KEY_ACTION => $action,
            ProcessAlertWorker::PARAM_KEY_CONTEXT => $context,
        ];

        // concurrent job, we do not care what order it is processed in
        $this->concurrentJobScheduler->addJob($jobData);
    }

    /**
     * @param $accountIds
     */
    public function calculateSellerRating($accountIds)
    {
        $jobData = [
            'task' => CalculateSellerRatingWorker::JOB_NAME,
            CalculateSellerRatingWorker::PARAM_KEY_ACCOUNT_IDS => $accountIds,
        ];

        // concurrent job, we do not care what order it is processed in
        $this->concurrentJobScheduler->addJob($jobData);
    }

    /**
     * @param $creditTransactionIds
     */
    public function logCreditTransaction($creditTransactionIds)
    {
        $jobData = [
            'task' => CreditTransactionLoggerWorker::JOB_NAME,
            CreditTransactionLoggerWorker::PARAM_KEY_CREDIT_TRANSACTION_IDS => $creditTransactionIds,
        ];

        // concurrent job, we do not care what order it is processed in
        $this->concurrentJobScheduler->addJob($jobData);
    }

    public function dailyCloseAuctionsWorker()
    {
        $jobData = [
            'task' => DailyCloseAuctionsWorker::JOB_NAME,
        ];

        // concurrent job, we do not care what order it is processed in
        $this->concurrentJobScheduler->addJob($jobData);
    }

    public function transferCreditForConfirmBills($ids, $context)
    {
        $jobData = [
            'task' => TransferCreditAfterConfirmBillWorker::JOB_NAME,
            TransferCreditAfterConfirmBillWorker::PARAM_KEY_BILL_IDS => $ids,
            TransferCreditAfterConfirmBillWorker::PARAM_KEY_CONTEXT => $context,
        ];

        // concurrent job, we do not care what order it is processed in
        $this->concurrentJobScheduler->addJob($jobData);
    }

    public function payFeeForNewAuctions($ids, $context)
    {
        $jobData = [
            'task' => PayFeeForNewAuctionWorker::JOB_NAME,
            PayFeeForNewAuctionWorker::PARAM_KEY_AUCTION_IDS => $ids,
            PayFeeForNewAuctionWorker::PARAM_KEY_CONTEXT => $context,
        ];

        // concurrent job, we do not care what order it is processed in
        $this->concurrentJobScheduler->addJob($jobData);
    }

    public function payFeeForConfirmBillWorker($ids, $context)
    {
        $jobData = [
            'task' => PayFeeForBillWorker::JOB_NAME,
            PayFeeForBillWorker::PARAM_KEY_BILL_IDS => $ids,
            PayFeeForBillWorker::PARAM_KEY_CONTEXT => $context,
        ];

        // concurrent job, we do not care what order it is processed in
        $this->concurrentJobScheduler->addJob($jobData);
    }

    public function returnFeeForRejectedBills($ids, $context)
    {
        $jobData = [
            'task' => ReturnFeeForRejectedBillWorker::JOB_NAME,
            ReturnFeeForRejectedBillWorker::PARAM_KEY_BILL_IDS => $ids,
            ReturnFeeForRejectedBillWorker::PARAM_KEY_CONTEXT => $context,
        ];

        // concurrent job, we do not care what order it is processed in
        $this->concurrentJobScheduler->addJob($jobData);
    }

    public function createWalletsForUser($ids, $context)
    {
        $jobData = [
            'task' => CreateWalletsForUserWorker::JOB_NAME,
            CreateWalletsForUserWorker::PARAM_KEY_USER_IDS => $ids,
            CreateWalletsForUserWorker::PARAM_KEY_CONTEXT => $context,
        ];

        // concurrent job, we do not care what order it is processed in
        $this->concurrentJobScheduler->addJob($jobData);
    }
}