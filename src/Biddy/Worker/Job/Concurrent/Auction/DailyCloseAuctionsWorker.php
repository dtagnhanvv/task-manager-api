<?php

namespace Biddy\Worker\Job\Concurrent\Auction;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Repository\Core\AuctionRepositoryInterface;
use Biddy\Service\Bidding\Core\AuctionCloserInterface;
use Biddy\Worker\Core\Job\JobInterface;
use Biddy\Worker\Core\JobParams;
use Biddy\Worker\Manager;
use Monolog\Logger;

class DailyCloseAuctionsWorker implements JobInterface
{
    const JOB_NAME = 'DailyCloseAuctionsWorker';
    const YESTERDAY = 'yesterday';

    /** @var Logger $logger */
    private $logger;

    /** @var AuctionCloserInterface */
    private $auctionCloser;

    /** @var AuctionRepositoryInterface */
    private $auctionRepository;

    /** @var Manager */
    private $worker;

    private $batchSize;

    /**
     * DailyCloseAuctionsWorker constructor.
     * @param Logger $logger
     * @param AuctionCloserInterface $auctionCloser
     * @param AuctionRepositoryInterface $auctionRepository
     * @param Manager $worker
     * @param $batchSize
     */
    public function __construct(Logger $logger, AuctionCloserInterface $auctionCloser, AuctionRepositoryInterface $auctionRepository, Manager $worker, $batchSize)
    {
        $this->logger = $logger;
        $this->auctionCloser = $auctionCloser;
        $this->auctionRepository = $auctionRepository;
        $this->worker = $worker;
        $this->batchSize = $batchSize;
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
        $endDate = date_create('now');

        $auctions = $this->auctionRepository->getAutomatedEndingProducts($endDate);

        $count = 0;
        foreach ($auctions as $auction) {
            if (!$auction instanceof AuctionInterface) {
                continue;
            }

            if ($auction->getStatus() == AuctionInterface::STATUS_CLOSED) {
                return;
            }

            if ($count > $this->batchSize) {
                $this->worker->dailyCloseAuctionsWorker();
                
                return;
            }

            try {
                $count++;
                $this->auctionCloser->closeAuction($auction);
            } catch (\Exception $e) {
            }
        }
    }
}