<?php

namespace Biddy\EventListener\Bidding;

use Biddy\Entity\Core\CreditTransaction;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\Core\WalletInterface;
use Biddy\Service\Util\UserUtilTrait;
use Biddy\Worker\Manager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class ReturnGuaranteeBidWhenBidChangeListener
{
    use UserUtilTrait;

    /** @var Manager */
    protected $workerManager;
    private $creditTransactions = [];

    /** @var EntityManagerInterface */
    private $em;
    private $returnBidStatus = [BidInterface::STATUS_CANCEL, BidInterface::STATUS_REJECTED, BidInterface::STATUS_LOOSE];

    /**
     * ReturnGuaranteeBidWhenCancelBidListener constructor.
     * @param Manager $workerManager
     */
    public function __construct(Manager $workerManager)
    {
        $this->workerManager = $workerManager;
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $bid = $args->getEntity();
        $em = $args->getEntityManager();

        if (!$bid instanceof BidInterface) {
            return;
        }

        if (!$args->hasChangedField('status') || !in_array($bid->getStatus(), $this->returnBidStatus)) {
            return;
        }

        $buyer = $bid->getBuyer();
        /** @var WalletInterface $basicWallet */
        $basicWallet = $buyer->getBasicWallet();
        /** @var WalletInterface $insureWallet */
        $insureWallet = $buyer->getInsureWallet();
        $detail = $this->getDetail($bid);

        $creditTransaction = new CreditTransaction();
        $creditTransaction
            ->setAmount($bid->getPrice())
            ->setFromWallet($insureWallet)
            ->setTargetWallet($basicWallet)
            ->setType(CreditTransactionInterface::TYPE_RETURN_GUARANTEED_BID)
            ->setTargetType(CreditTransactionInterface::TARGET_TYPE_BID)
            ->setTargetId($bid->getId())
            ->setDetail($detail);

        $this->creditTransactions[] = $creditTransaction;

        $bid->setBuyer($buyer);
        $em->merge($bid);
    }

    /**
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $this->em = $event->getEntityManager();

        if (empty($this->creditTransactions)) {
            return;
        }

        $creditTransactions = $this->creditTransactions;
        $this->creditTransactions = [];
        $count = 0;

        foreach ($creditTransactions as $creditTransaction) {
            if (!$creditTransaction instanceof CreditTransactionInterface) {
                continue;
            }

            $this->em->persist($creditTransaction);
            $count++;
        }

        if (!empty($count)) {
            $this->em->flush();
        }
    }

    /**
     * @param BidInterface $bid
     * @return string
     */
    private function getDetail(BidInterface $bid)
    {
        $product = $bid->getAuction()->getProduct();

        switch ($bid->getStatus()) {
            case BidInterface::STATUS_CANCEL:
                return sprintf("Hủy đấu giá, trả lại tiền cược %s credit cho sản phẩm %s (ID: %s)", $bid->getPrice(), $product->getSubject(), $product->getId());
            case BidInterface::STATUS_REJECTED:
                return sprintf("Hủy giao dịch, trả lại tiền cược %s credit cho sản phẩm %s (ID: %s)", $bid->getPrice(), $product->getSubject(), $product->getId());
            case BidInterface::STATUS_LOOSE:
                return sprintf("Chi trả lại %s tiền cược cho lần đấu giá thất bại với sản phẩm %s (ID: %s)", $bid->getPrice(), $product->getSubject(), $product->getId());
        }

        return 'Trả lại tiền đấu giá đã cược';
    }
}