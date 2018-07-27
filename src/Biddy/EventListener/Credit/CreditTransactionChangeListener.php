<?php

namespace Biddy\EventListener\Credit;

use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Service\Util\PublicSimpleException;
use Biddy\Worker\Manager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Monolog\Logger;

class CreditTransactionChangeListener
{
    /** @var Manager */
    protected $workerManager;

    /** @var Logger */
    private $logger;

    private $creditTransactionNotEnoughCredit;

    /**
     * CalculateSellerRatingWhenProductRatingChangeListener constructor.
     * @param Manager $workerManager
     * @param Logger $logger
     * @param $creditTransactionNotEnoughCredit
     */
    public function __construct(Manager $workerManager, Logger $logger, $creditTransactionNotEnoughCredit)
    {
        $this->workerManager = $workerManager;
        $this->logger = $logger;
        $this->creditTransactionNotEnoughCredit = $creditTransactionNotEnoughCredit;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if (!$entity instanceof CreditTransactionInterface) {
            return;
        }

        $this->checkForValidCredit($entity);
        $this->changeCredit($entity, $em);
    }

    /**
     * @param CreditTransactionInterface $creditTransaction
     * @throws PublicSimpleException
     */
    private function checkForValidCredit(CreditTransactionInterface $creditTransaction)
    {
        if (!is_numeric($creditTransaction->getAmount())) {
            throw  new PublicSimpleException('Amount not set');
        }

        $fromWallet = $creditTransaction->getFromWallet();
        if ($fromWallet->getOwner() instanceof AdminInterface) {
            return;
        }

        if (empty($fromWallet->getCredit()) || $fromWallet->getCredit() < $creditTransaction->getAmount()) {
            throw  new PublicSimpleException($this->creditTransactionNotEnoughCredit);
        }
    }

    /**
     * @param CreditTransactionInterface $creditTransaction
     * @param EntityManagerInterface $em
     */
    private function changeCredit(CreditTransactionInterface $creditTransaction, EntityManagerInterface $em)
    {
        try {
            $fromWallet = $creditTransaction->getFromWallet();
            $targetWallet = $creditTransaction->getTargetWallet();

            $fromWallet->setCredit($fromWallet->getCredit() - $creditTransaction->getAmount());
            $targetWallet->setCredit($targetWallet->getCredit() + $creditTransaction->getAmount());

            $creditTransaction->setFromWallet($fromWallet);
            $creditTransaction->setTargetWallet($targetWallet);
            $em->persist($creditTransaction);
            $em->flush();

            $this->workerManager->logCreditTransaction($creditTransaction->getId());
        } catch (\Exception $e) {
            $this->logger->warning(sprintf("Error on change credit: %s", $e));
        }
    }
}