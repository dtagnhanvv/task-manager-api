<?php

namespace Biddy\Service\Bidding\Core;

use Biddy\DomainManager\AuctionManagerInterface;
use Biddy\DomainManager\BidManagerInterface;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Service\Util\PublicSimpleException;
use Biddy\Worker\Manager;
use Psr\Log\LoggerInterface;

class AuctionCloser implements AuctionCloserInterface
{
    private $auctionRules;

    /** @var LoggerInterface */
    private $logger;

    /** @var BidManagerInterface */
    private $bidManager;

    /** @var BidStatusManagerInterface */
    private $bidStatusManager;

    /** @var AuctionManagerInterface */
    private $auctionManager;

    /**
     * AutomatedAuctionManager constructor.
     * @param LoggerInterface $logger
     * @param AuctionManagerInterface $auctionManager
     * @param Manager $manager
     * @param BidManagerInterface $bidManager
     * @param BidStatusManagerInterface $bidStatusManager
     * @param $auctionRules
     */
    public function __construct(LoggerInterface $logger, AuctionManagerInterface $auctionManager, Manager $manager,
                                BidManagerInterface $bidManager,
                                BidStatusManagerInterface $bidStatusManager,
                                $auctionRules)
    {
        $this->auctionRules = $auctionRules;
        $this->logger = $logger;
        $this->bidManager = $bidManager;
        $this->bidStatusManager = $bidStatusManager;
        $this->auctionManager = $auctionManager;
    }

    /**
     * @inheritdoc
     */
    public function getAuctionRuleForProduct(AuctionInterface $auction, $action = AuctionRuleInterface::AUTOMATED)
    {
        foreach ($this->auctionRules as $rule) {
            if (!$rule instanceof AuctionRuleInterface) {
                continue;
            }

            if ($rule->supportProduct($auction, $action)) {
                return $rule;
            }
        }

        throw new PublicSimpleException(sprintf('Can not find auction rule for auction: %s', $auction->getId()));
    }

    /**
     * @inheritdoc
     */
    public function closeAuction(AuctionInterface $auction, $params = [])
    {
        if ($auction->getStatus() == AuctionInterface::STATUS_CLOSED) {
            return;
        }
        
        $this->logger->info(sprintf("Try to close auction %s, on product %s", $auction->getId(), $auction->getProduct()->getId()));

        $action = isset($params['ruleType']) ? $params['ruleType'] : null;
        $action = empty($auction) || is_null($action) ? $auction->getType() : $action;

        try {
            $rule = $this->getAuctionRuleForProduct($auction, $action);
            $winningBids = $rule->getWinningBids($auction, $params);

            $auction->setStatus(AuctionInterface::STATUS_CLOSED);
            $this->bidStatusManager->createBillForWinningBids($winningBids);
            $this->bidStatusManager->changeStatusOfLooseBidsOnProduct($auction, $winningBids);
            $this->auctionManager->save($auction);
            $this->logger->info(sprintf("Success on close auction %s, on product %s", $auction->getId(), $auction->getProduct()->getId()));
        } catch (\Exception $e) {
            $this->logger->error($e);

            $this->logger->warning(sprintf("Failure on close auction %s, on product %s", $auction->getId(), $auction->getProduct()->getId()));
            throw new PublicSimpleException($e->getMessage());
        }
    }
}