<?php

namespace Biddy\Service\Auditing\CreditTransaction;


interface CreditTransactionLoggerInterface
{
    /**
     * @param $creditTransactionIds
     * @return mixed
     */
    public function logFileForCreditTransactions($creditTransactionIds);
}