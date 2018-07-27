<?php

namespace Biddy\Service\Auditing\CreditTransaction;

use Biddy\DomainManager\CreditTransactionManagerInterface;
use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\User\UserEntityInterface;
use Biddy\Service\Util\UserUtilTrait;
use Biddy\Service\Util\CsvWriterInterface;

class CreditTransactionLogger implements CreditTransactionLoggerInterface
{
    use UserUtilTrait;

    /** @var CreditTransactionManagerInterface */
    private $creditTransactionManager;

    /** @var CsvWriterInterface */
    private $csvWriter;

    private $creditLoggingFolderPath;

    private $headers = [
        'CreatedDate',
        'Transaction Id',
        'From Wallet',
        'Target Wallet',

        'Amount',
        'Type',
        'Detail',

        'From User',
        'Target User',
    ];

    /**
     * CreditTransactionLogger constructor.
     * @param CreditTransactionManagerInterface $creditTransactionManager
     * @param CsvWriterInterface $csvWriter
     * @param $creditLoggingFolderPath
     */
    public function __construct(CreditTransactionManagerInterface $creditTransactionManager, CsvWriterInterface $csvWriter, $creditLoggingFolderPath)
    {
        $this->creditTransactionManager = $creditTransactionManager;
        $this->csvWriter = $csvWriter;
        $this->creditLoggingFolderPath = $creditLoggingFolderPath;
    }

    /**
     * @param $creditTransactionIds
     * @return mixed
     */
    public function logFileForCreditTransactions($creditTransactionIds)
    {
        $creditTransactionIds = is_array($creditTransactionIds) ? $creditTransactionIds : [$creditTransactionIds];

        foreach ($creditTransactionIds as $creditTransactionId) {
            $creditTransaction = $this->creditTransactionManager->find($creditTransactionId);
            if (!$creditTransaction instanceof CreditTransactionInterface) {
                continue;
            }

            $this->logFileForAuditing($creditTransaction);
        }
    }

    /**
     * @param CreditTransactionInterface $creditTransaction
     */
    private function logFileForAuditing(CreditTransactionInterface $creditTransaction)
    {
        $fromUser = $creditTransaction->getFromWallet()->getOwner();
        $targetUser = $creditTransaction->getTargetWallet()->getOwner();

        $this->logFileForUser($creditTransaction, $fromUser);

        if ($fromUser->getId() != $targetUser->getId()) {
            $this->logFileForUser($creditTransaction, $targetUser);
        }
    }

    /**
     * @param CreditTransactionInterface $creditTransaction
     * @param UserEntityInterface $user
     */
    private function logFileForUser(CreditTransactionInterface $creditTransaction, UserEntityInterface $user)
    {
        $content = $this->getBasicInfoOfCreditTransaction($creditTransaction);

        $logFilePath = $this->getLogFilePath($user, $creditTransaction->getCreatedDate());

        $this->csvWriter->write($logFilePath, $content, $this->headers);
    }

    /**
     * @param UserEntityInterface $user
     * @param $createdDate
     * @return string
     */
    private function getLogFilePath(UserEntityInterface $user, $createdDate)
    {
        if (!$createdDate instanceof \DateTime) {
            $createdDate = date_create($createdDate);
        }

        $sections = [];
        $sections[] = $this->getUserType($user);
        $sections[] = $createdDate->format("Y");
        $sections[] = sprintf("Group_%s", round($user->getId() / 1000));
        $sections[] = sprintf("User_%s", $user->getId());
        $sections[] = sprintf("User_%s_date_%s.csv", $user->getId(), $createdDate->format('Y-m-d'));

        $path = sprintf("%s/%s", $this->creditLoggingFolderPath, implode("/", $sections));

        return $path;
    }

    /**
     * @param CreditTransactionInterface $creditTransaction
     * @return mixed
     */
    private function getBasicInfoOfCreditTransaction(CreditTransactionInterface $creditTransaction)
    {
        $fromWallet = $creditTransaction->getFromWallet();
        $targetWallet = $creditTransaction->getTargetWallet();

        $fromUser = $fromWallet->getOwner();
        $targetUser = $targetWallet->getOwner();

        return [
            $creditTransaction->getCreatedDate()->format("Y-m-d H:i:s"),
            $creditTransaction->getId(),
            sprintf("%s (%s)", $fromWallet->getName(), $fromWallet->getId()),
            sprintf("%s (%s)", $targetWallet->getName(), $targetWallet->getId()),

            $creditTransaction->getAmount(),
            $creditTransaction->getType(),
            $creditTransaction->getDetail(),

            sprintf("%s (%s)", $fromUser->getUsername(), $fromUser->getId()),
            sprintf("%s (%s)", $targetUser->getUsername(), $targetUser->getId()),
        ];
    }
}