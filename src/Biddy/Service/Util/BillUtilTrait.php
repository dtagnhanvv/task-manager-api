<?php


namespace Biddy\Service\Util;

use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\BillInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Core\ProductRatingInterface;

trait BillUtilTrait
{
    use UserUtilTrait;

    /**
     * @param $bills
     * @return array
     */
    public function serializeBills($bills)
    {
        $groups = [];
        foreach ($bills as $bill) {
            if (!$bill instanceof BillInterface) {
                continue;
            }
            $groups[] = $this->serializeSingleBill($bill);
        }

        return $groups;
    }

    /**
     * @param BillInterface $bill
     * @return array
     */
    public function serializeSingleBill(BillInterface $bill)
    {
        $group = [];
        $group['createdDate'] = $bill->getCreatedDate();
        $group['id'] = $bill->getId();
        $group['noteForSeller'] = $bill->getNoteForSeller();
        $group['payment'] = $bill->getPayment();
        $group['price'] = $bill->getPrice();
        $group['status'] = $bill->getStatus();
        $group['seller'] = $this->serializeSingleUser($bill->getSeller());
        $group['buyer'] = $this->serializeSingleUser($bill->getBuyer());
        $group['content'] = $this->getContentOfBill($bill);
        $group['rating'] = $this->getRatingOfBill($bill);
        $group['product'] = $this->getProductOfBill($bill);

        return $group;
    }

    /**
     * @param BillInterface $bill
     * @return mixed|string
     */
    public function getRatingOfBill(BillInterface $bill)
    {
        $productRating = $bill->getProductRating();

        if (!$productRating instanceof ProductRatingInterface) {
            return '';
        }

        return $productRating->getRateValue();
    }

    /**
     * @param BillInterface $bill
     * @return mixed|string
     */
    public function getContentOfBill(BillInterface $bill)
    {
        $bid = $bill->getBid();

        if (!$bid instanceof BidInterface) {
            return '';
        }

        return $bid->getAuction()->getProduct()->getSubject();
    }

    /**
     * @param BillInterface $bill
     * @return mixed|string
     */
    public function getProductOfBill(BillInterface $bill)
    {
        $bid = $bill->getBid();

        if (!$bid instanceof BidInterface) {
            return [];
        }

        /** @var ProductInterface $product */
        $product = $bid->getAuction()->getProduct();

        $group['id'] = $product->getId();
        $group['content'] = $product->getSubject();

        return $group;
    }
}