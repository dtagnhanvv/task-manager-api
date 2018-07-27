<?php


namespace Biddy\Service\Util;

use Biddy\DomainManager\AuctionManagerInterface;
use Biddy\Entity\Core\Alert;
use Biddy\Model\Core\AlertInterface;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\BillInterface;
use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\SaleInterface;

trait AlertUtilTrait
{
    public function createAlert(ModelInterface $model)
    {
        $alert = new Alert();
        $alert->setTargetId($model->getId());

        if ($model instanceof ProductInterface) {
            $alert = $this->buildMoreInfoOnProductAlert($alert, $model);
        }

        if ($model instanceof AuctionInterface) {
            $alert = $this->buildMoreInfoOnAuctionAlert($alert, $model);
        }

        if ($model instanceof BidInterface) {
            $alert = $this->buildMoreInfoOnBidAlert($alert, $model);
        }

        if ($model instanceof BillInterface) {
            $alert = $this->buildMoreInfoOnBillAlert($alert, $model);
        }

        if ($model instanceof AccountInterface) {
            $alert = $this->buildMoreInfoOnAccountAlert($alert, $model);
        }

        if ($model instanceof SaleInterface) {
            $alert = $this->buildMoreInfoOnSaleAlert($alert, $model);
        }

        if ($model instanceof CreditTransactionInterface) {
            $alert = $this->buildMoreInfoOnCreditTransactionAlert($alert, $model);
        }

        return $alert;
    }

    /**
     * @param $alerts
     * @param AuctionManagerInterface $auctionManager
     * @return array
     */
    public function serializeAlerts($alerts, AuctionManagerInterface $auctionManager)
    {
        $groups = [];
        foreach ($alerts as $alert) {
            if (!$alert instanceof AlertInterface) {
                continue;
            }

            $group = [];
            $group['id'] = $alert->getId();
            $group['detail'] = $alert->getDetail();
            $group['account'] = $alert->getAccount();

            $group['code'] = $alert->getCode();
            $group['createdDate'] = $alert->getCreatedDate();
            $group['isRead'] = $alert->getIsRead();
            $group['type'] = $alert->getType();

            $group['targetType'] = $alert->getTargetType();
            $group['targetId'] = $alert->getTargetId();

            if ($alert->getTargetType() == AlertInterface::TARGET_TYPE_AUCTION) {
                $auction = $auctionManager->find($alert->getTargetId());
                if ($auction instanceof AuctionInterface) {
                    $group['product'] = $auction->getProduct()->getId();
                }
            }

            if ($alert->getTargetType() == AlertInterface::TARGET_TYPE_PRODUCT_AUCTION) {
                $auction = $auctionManager->find($alert->getTargetId());
                if ($auction instanceof AuctionInterface) {
                    $group['product'] = $auction->getProduct()->getId();
                }
            }

            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * @param AlertInterface $alert
     * @param ProductInterface $model
     * @return AlertInterface
     */
    public function buildMoreInfoOnProductAlert(AlertInterface $alert, ProductInterface $model)
    {
        $alert->setTargetType(AlertInterface::TARGET_TYPE_PRODUCT);
        $alert->setAccount($model->getSeller());

        return $alert;
    }

    /**
     * @param AlertInterface $alert
     * @param AuctionInterface $model
     * @return AlertInterface
     */
    public function buildMoreInfoOnAuctionAlert(AlertInterface $alert, AuctionInterface $model)
    {
        $alert->setTargetType(AlertInterface::TARGET_TYPE_AUCTION);
        $alert->setAccount($model->getProduct()->getSeller());

        return $alert;
    }

    /**
     * @param AlertInterface $alert
     * @param BidInterface $model
     * @return AlertInterface
     */
    public function buildMoreInfoOnBidAlert(AlertInterface $alert, BidInterface $model)
    {
        $alert->setTargetType(AlertInterface::TARGET_TYPE_BID);
        $alert->setAccount($model->getAuction()->getProduct()->getSeller());

        return $alert;
    }

    /**
     * @param AlertInterface $alert
     * @param BillInterface $model
     * @return AlertInterface
     */
    public function buildMoreInfoOnBillAlert(AlertInterface $alert, BillInterface $model)
    {
        $alert->setTargetType(AlertInterface::TARGET_TYPE_BILL);
        $alert->setAccount($model->getBuyer());

        return $alert;
    }

    /**
     * @param AlertInterface $alert
     * @param AccountInterface $model
     * @return AlertInterface
     */
    public function buildMoreInfoOnAccountAlert(AlertInterface $alert, AccountInterface $model)
    {
        $alert->setTargetType(AlertInterface::TARGET_TYPE_ACCOUNT);
        $alert->setAccount($model);

        return $alert;
    }

    /**
     * @param AlertInterface $alert
     * @param SaleInterface $model
     * @return AlertInterface
     */
    public function buildMoreInfoOnSaleAlert(AlertInterface $alert, SaleInterface $model)
    {
        $alert->setTargetType(AlertInterface::TARGET_TYPE_SALE);
        $alert->setAccount($model);

        return $alert;
    }

    /**
     * @param AlertInterface $alert
     * @param CreditTransactionInterface $creditTransactionInterface
     * @return AlertInterface
     */
    public function buildMoreInfoOnCreditTransactionAlert(AlertInterface $alert, CreditTransactionInterface $creditTransactionInterface)
    {
        $alert->setTargetType(AlertInterface::TARGET_TYPE_CREDIT);

        return $alert;
    }
}