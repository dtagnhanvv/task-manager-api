<?php


namespace Biddy\Service\Util;

use Biddy\Model\Core\CreditTransactionInterface;

trait CreditTransactionUtilTrait
{
    use WalletUtilTrait;
    
    /**
     * @param $creditTransactions
     * @return array
     */
    public function serializeCreditTransactions($creditTransactions)
    {
        $groups = [];
        foreach ($creditTransactions as $creditTransaction) {
            if (!$creditTransaction instanceof CreditTransactionInterface) {
                continue;
            }

            $group = [];
            $group['id'] = $creditTransaction->getId();
            $group['amount'] = $creditTransaction->getAmount();
            $group['detail'] = $creditTransaction->getDetail();
            $group['createdDate'] = $creditTransaction->getCreatedDate();
            $group['type'] = $creditTransaction->getType();
            $group['targetType'] = $creditTransaction->getTargetType();
            $group['targetId'] = $creditTransaction->getTargetId();
            $group['fromWallet'] = $this->serializeSingWallet($creditTransaction->getFromWallet());
            $group['targetWallet'] = $this->serializeSingWallet($creditTransaction->getTargetWallet());

            $groups[] = $group;
        }

        return $groups;
    }
}