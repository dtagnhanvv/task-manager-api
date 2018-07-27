<?php

namespace Biddy\Worker\Job\Concurrent\Fee;

use Biddy\DomainManager\AuctionManagerInterface;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Service\Bidding\Core\BidCreditManagerInterface;
use Biddy\Worker\Core\Job\JobInterface;
use Biddy\Worker\Core\JobParams;
use Monolog\Logger;

class PayFeeForNewAuctionWorker implements JobInterface
{
    const JOB_NAME = 'PayFeeForNewAuctionWorker';
    const PARAM_KEY_AUCTION_IDS = 'auction_ids';
    const PARAM_KEY_CONTEXT = 'context';

    /** @var Logger $logger */
    private $logger;

    /** @var BidCreditManagerInterface */
    private $bidCreditManager;

    /** @var AuctionManagerInterface */
    private $auctionManager;

    /**
     * PayFeeForNewAuctionWorker constructor.
     * @param Logger $logger
     * @param BidCreditManagerInterface $bidCreditManager
     * @param AuctionManagerInterface $auctionManager
     */
    public function __construct(Logger $logger, BidCreditManagerInterface $bidCreditManager, AuctionManagerInterface $auctionManager)
    {
        $this->logger = $logger;
        $this->bidCreditManager = $bidCreditManager;
        $this->auctionManager = $auctionManager;
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
        $auctionIds = $params->getRequiredParam(self::PARAM_KEY_AUCTION_IDS);

        if (empty($auctionIds)) {
            return;
        }

        foreach ($auctionIds as $id) {
            $auction = $this->auctionManager->find($id);
            if (!$auction instanceof AuctionInterface) {
                continue;
            }

            $this->bidCreditManager->payFeeForNewAuction($auction);
        }
    }
}