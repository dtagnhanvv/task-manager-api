<?php

namespace Biddy\Service\User;

use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\DomainManager\ProductManagerInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Core\ProductRatingInterface;
use Biddy\Model\User\Role\AccountInterface;

class SellerRatingCalculator implements SellerRatingCalculatorInterface
{
    /** @var AccountManagerInterface */
    private $accountManager;

    /** @var ProductManagerInterface */
    private $productManager;

    /**
     * SellerRatingCalculator constructor.
     * @param AccountManagerInterface $accountManager
     * @param ProductManagerInterface $productManager
     */
    public function __construct(AccountManagerInterface $accountManager, ProductManagerInterface $productManager)
    {
        $this->accountManager = $accountManager;
        $this->productManager = $productManager;
    }

    /**
     * @inheritdoc
     */
    public function calculateRatingForUsers($accountIds)
    {
        $accountIds = explode(",", $accountIds);
        $accountIds = array_map(function ($accountId) {
            return floatval(trim($accountId));
        }, $accountIds);

        foreach ($accountIds as $accountId) {
            if (empty($accountId)) {
                continue;
            }

            $seller = $this->accountManager->find($accountId);
            if (!$seller instanceof AccountInterface) {
                continue;
            }

            $this->recalculatingRatingForSingleUser($seller);
        }
    }

    /**
     * @param AccountInterface $seller
     */
    private function recalculatingRatingForSingleUser(AccountInterface $seller)
    {
        $products = $this->productManager->getProductsForAccountQuery($seller);
        $sellerRateValues = [];

        foreach ($products as $product) {
            if (!$product instanceof ProductInterface) {
                continue;
            }

            $productRatings = $product->getProductRatings();
            $productRateValues = [];

            foreach ($productRatings as $productRating) {
                if (!$productRating instanceof ProductRatingInterface) {
                    continue;
                }

                $productRateValues[] = $productRating->getRateValue();
                $sellerRateValues[] = $productRating->getRateValue();
            }

            $averageRatingForProduct = $this->getAverageRating($productRateValues);
            $averageRatingForProduct = number_format($averageRatingForProduct, 1);
            $product->setRating($averageRatingForProduct);
            $this->productManager->save($product);
        }

        $averageRatingForSeller = $this->getAverageRating($sellerRateValues);
        $averageRatingForSeller = number_format($averageRatingForSeller, 1);
        $seller->setRating($averageRatingForSeller);
        $this->accountManager->save($seller);
    }

    /**
     * @param $rateValues
     * @return float|null
     */
    private function getAverageRating($rateValues)
    {
        if (empty($rateValues)) {
            return null;
        }

        $rateValues = is_array($rateValues) ? $rateValues : [$rateValues];
        $rateValues = array_filter($rateValues);
        $rateValues = array_map(function ($rate) {
            return floatval($rate);
        }, $rateValues);

        return array_sum($rateValues) / count($rateValues);
    }
}