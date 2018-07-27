<?php

namespace Biddy\Worker\Job\Concurrent\Rating;

use Biddy\Service\User\SellerRatingCalculatorInterface;
use Biddy\Worker\Core\Job\JobInterface;
use Biddy\Worker\Core\JobParams;
use Monolog\Logger;

class CalculateSellerRatingWorker implements JobInterface
{
    const JOB_NAME = 'CalculateSellerRatingWorker';
    const PARAM_KEY_ACCOUNT_IDS = 'accountIds';

    /** @var Logger $logger */
    private $logger;

    /** @var SellerRatingCalculatorInterface */
    private $sellerRatingCalculator;

    /**
     * CalculateSellerRatingWorker constructor.
     * @param Logger $logger
     * @param SellerRatingCalculatorInterface $sellerRatingCalculator
     * @internal param EntityManagerInterface $em
     */
    public function __construct(Logger $logger, SellerRatingCalculatorInterface $sellerRatingCalculator)
    {
        $this->logger = $logger;
        $this->sellerRatingCalculator = $sellerRatingCalculator;
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
        $accountIds = $params->getRequiredParam(self::PARAM_KEY_ACCOUNT_IDS);

        if (empty($accountIds)) {
            return;
        }

        $this->sellerRatingCalculator->calculateRatingForUsers($accountIds);
    }
}