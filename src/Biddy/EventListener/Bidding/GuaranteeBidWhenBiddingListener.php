<?php

namespace Biddy\EventListener\Bidding;

use Biddy\Entity\Core\CreditTransaction;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\Core\WalletInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Service\Util\UserUtilTrait;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

class GuaranteeBidWhenBiddingListener
{
    use UserUtilTrait;

    /** @var Manager */
    protected $workerManager;
    protected $buyers = [];

    /**
     * GuaranteeBidWhenBiddingListener constructor.
     * @param Manager $workerManager
     */
    public function __construct(Manager $workerManager)
    {
        $this->workerManager = $workerManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $bid = $args->getEntity();
        $em = $args->getEntityManager();

        if (!$bid instanceof BidInterface) {
            return;
        }

        $product = $bid->getAuction()->getProduct();
        $buyer = $bid->getBuyer();

        /** @var WalletInterface $basicWallet */
        $basicWallet = $buyer->getBasicWallet();
        /** @var WalletInterface $insureWallet */
        $insureWallet = $buyer->getInsureWallet();

        $creditTransaction = new CreditTransaction();
        $creditTransaction
            ->setAmount($bid->getPrice())
            ->setFromWallet($basicWallet)
            ->setTargetWallet($insureWallet)
            ->setType(CreditTransactionInterface::TYPE_GUARANTEED_BID)
            ->setTargetType(CreditTransactionInterface::TARGET_TYPE_BID)
            ->setTargetId($bid->getId())
            ->setDetail(sprintf("Trừ %s credit tiền cược cho đấu giá sản phẩm %s: (ID: %s)", $bid->getPrice(), $product->getSubject(), $product->getId()));
        
        $em->persist($creditTransaction);

        $this->buyers[$buyer->getId()] = $buyer;
    }

    /**
     * @param PostFlushEventArgs $event
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $buyers = $this->buyers;
        $this->buyers = [];
        $count = 0;

        foreach ($buyers as $buyer) {
            if (!$buyer instanceof UserRoleInterface) {
                continue;
            }

            $em->merge($buyer);
            $count++;
        }

        if ($count > 0) {
            $em->flush();
        }
    }
}