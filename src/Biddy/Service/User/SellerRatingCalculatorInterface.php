<?php

namespace Biddy\Service\User;


interface SellerRatingCalculatorInterface
{
    /**
     * @param $accountIds
     * @return mixed
     */
    public function calculateRatingForUsers($accountIds);
}