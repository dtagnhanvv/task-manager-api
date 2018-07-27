<?php

namespace Biddy\Service\Alert;


use Biddy\Bundle\UserSystem\AccountBundle\Entity\User;
use Biddy\Entity\Core\Auction;
use Biddy\Entity\Core\Bid;
use Biddy\Entity\Core\Bill;
use Biddy\Entity\Core\CreditTransaction;
use Biddy\Entity\Core\Product;
use Biddy\EventListener\Alert\Auction\CreateAlertForAuctionChangeListener;
use Biddy\EventListener\Alert\Bid\CreateAlertForBidChangeListener;
use Biddy\EventListener\Alert\Bill\CreateAlertForBillChangeListener;
use Biddy\EventListener\Alert\Credit\CreateAlertForAccountCreditChangeListener;
use Biddy\EventListener\Alert\Credit\CreateAlertForCreditTransactionChangeListener;
use Biddy\EventListener\Alert\Credit\CreateAlertForSaleCreditChangeListener;
use Biddy\EventListener\Alert\Product\CreateAlertForProductChangeListener;
use Biddy\EventListener\Alert\Profile\CreateAlertForAccountChangeListener;
use Biddy\EventListener\Alert\Profile\CreateAlertForSaleChangeListener;
use Biddy\Model\Core\AlertInterface;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\BillInterface;
use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Core\WalletInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\SaleInterface;
use Biddy\Service\Util\AlertUtilTrait;
use Doctrine\ORM\EntityManagerInterface;

class ProcessAlert implements ProcessAlertInterface
{
    use AlertUtilTrait;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function createAlerts($objectType, $objectIds, $action, $context)
    {
        if (empty($objectIds)) {
            return;
        }

        switch ($context) {
            case CreateAlertForAuctionChangeListener::class:
                if ($action == ProcessAlertInterface::ACTION_CREATE) {
                    $this->createAlertForNewAuction($objectIds);
                }
                if ($action == ProcessAlertInterface::ACTION_UPDATE) {
                    $this->createAlertForUpdateAuction($objectIds);
                }
                break;
            case CreateAlertForBidChangeListener::class:
                if ($action == ProcessAlertInterface::ACTION_CREATE) {
                    $this->createAlertForNewBid($objectIds);
                }
                if ($action == ProcessAlertInterface::ACTION_UPDATE) {
                    $this->createAlertForUpdateBid($objectIds);
                }
                break;
            case CreateAlertForBillChangeListener::class:
                if ($action == ProcessAlertInterface::ACTION_CREATE) {
                    $this->createAlertForNewBill($objectIds);
                }
                if ($action == ProcessAlertInterface::ACTION_UPDATE) {
                    $this->createAlertForUpdateBill($objectIds);
                }
                break;
            case CreateAlertForAccountCreditChangeListener::class:
                if ($action == ProcessAlertInterface::ACTION_UPDATE) {
                    $this->createAlertForUpdateAccountCredit($objectIds);
                }
                break;
            case CreateAlertForCreditTransactionChangeListener::class:
                if ($action == ProcessAlertInterface::ACTION_CREATE) {
                    $this->createAlertForNewCreditTransaction($objectIds);
                }
                break;
            case CreateAlertForSaleCreditChangeListener::class:
                if ($action == ProcessAlertInterface::ACTION_UPDATE) {
                    $this->createAlertForUpdateSaleCredit($objectIds);
                }
                break;
            case CreateAlertForProductChangeListener::class:
                if ($action == ProcessAlertInterface::ACTION_CREATE) {
                    $this->createAlertForNewProduct($objectIds);
                }
                if ($action == ProcessAlertInterface::ACTION_UPDATE) {
                    $this->createAlertForUpdateProduct($objectIds);
                }
                break;
            case CreateAlertForAccountChangeListener::class:
                if ($action == ProcessAlertInterface::ACTION_CREATE) {
                    $this->createAlertForNewAccount($objectIds);
                }
                if ($action == ProcessAlertInterface::ACTION_UPDATE) {
                    $this->createAlertForUpdateAccount($objectIds);
                }
                break;
            case CreateAlertForSaleChangeListener::class:
                if ($action == ProcessAlertInterface::ACTION_CREATE) {
                    $this->createAlertForNewSale($objectIds);
                }
                if ($action == ProcessAlertInterface::ACTION_UPDATE) {
                    $this->createAlertForUpdateSale($objectIds);
                }
                break;
        }

        $this->em->flush();
    }

    /**
     * @param $ids
     */
    private function createAlertForNewAuction($ids)
    {
        $repository = $this->em->getRepository(Auction::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof AuctionInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);

            if (empty($alert->getDetail())) {
                $alert->setDetail(sprintf('Tạo mới phiên đấu giá %s, cho sản phẩm %s', $entity->getId(), $entity->getProduct()->getSubject()));
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForUpdateAuction($ids)
    {
        $repository = $this->em->getRepository(Auction::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof AuctionInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);

            if (empty($alert->getDetail())) {
                if ($entity->getStatus() == AuctionInterface::STATUS_CLOSED) {
                    $alert->setTargetType(AlertInterface::TARGET_TYPE_PRODUCT_AUCTION);
                    $alert->setDetail(sprintf('Kết thúc phiên đấu giá %s', $entity->getId()));
                } else {
                    $alert->setDetail(sprintf('Cập nhật phiên đấu giá %s', $entity->getId()));
                }
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForNewBid($ids)
    {
        $repository = $this->em->getRepository(Bid::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof BidInterface) {
                //Wait for sync bid from memory to database
                //Bad code, but work
                sleep(3);
                $entity = $repository->find($id);
            }
            if (!$entity instanceof BidInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);
            //Notify to product owner about new bids from other
            $alert->setAccount($entity->getAuction()->getProduct()->getSeller());
            $alert->setTargetType(AlertInterface::TARGET_TYPE_PRODUCT_AUCTION);
            $alert->setTargetId($entity->getAuction()->getId());

            if (empty($alert->getDetail())) {
                $alert->setDetail(sprintf('Tạo mới đấu giá %s', $entity->getAuction()->getProduct()->getSubject()));
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForUpdateBid($ids)
    {
        $repository = $this->em->getRepository(Bid::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof BidInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);
            //Notify to buyer
            $alert->setAccount($entity->getBuyer());

            //Show product detail with all bids
            $alert->setTargetType(AlertInterface::TARGET_TYPE_PRODUCT_AUCTION);
            $alert->setTargetId($entity->getAuction()->getId());

            if (empty($alert->getDetail())) {
                if ($entity->getStatus() == BidInterface::STATUS_WIN) {
                    $alert->setDetail(sprintf('%s chiến thắng trong phiên đấu giá %s với giá thầu %s', $entity->getBuyer()->getUsername(), $entity->getAuction()->getId(), $entity->getPrice()));
                } elseif ($entity->getStatus() == BidInterface::STATUS_LOOSE) {
                    $alert->setDetail(sprintf('%s thua trong phiên đấu giá %s với giá thầu %s', $entity->getBuyer()->getUsername(), $entity->getAuction()->getId(), $entity->getPrice()));
                } elseif ($entity->getStatus() == BidInterface::STATUS_CANCEL) {
                    $alert->setDetail(sprintf('Hủy đấu giá tại phiên %s', $entity->getAuction()->getId()));
                } else {
                    $alert->setDetail(sprintf('Cập nhật đấu giá %s', $entity->getId()));
                }
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForNewBill($ids)
    {
        $repository = $this->em->getRepository(Bill::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof BillInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);
            if (empty($alert->getDetail())) {
                $bid = $entity->getBid();

                if ($bid instanceof BidInterface) {
                    $alert->setDetail(sprintf('Tạo hợp đồng từ sản phẩm %s, với giá %s', $bid->getAuction()->getProduct()->getSubject(), $bid->getPrice()));
                } else {
                    $alert->setDetail(sprintf('Tạo hợp đồng %s', $entity->getId()));
                }
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForUpdateBill($ids)
    {
        $repository = $this->em->getRepository(Bill::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof BillInterface) {
                continue;
            }
            $alert = $this->createAlert($entity);
            if (empty($alert->getDetail())) {
                $alert->setDetail(sprintf('Cập nhật hợp đồng %s', $entity->getId()));
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForUpdateAccountCredit($ids)
    {
        $repository = $this->em->getRepository(User::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof AccountInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);
            $alert->setTargetType(AlertInterface::TARGET_TYPE_CREDIT);

            if (empty($alert->getDetail())) {
                $alert->setDetail('Cập nhật ví');
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForNewCreditTransaction($ids)
    {
        $repository = $this->em->getRepository(CreditTransaction::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof CreditTransactionInterface) {
                sleep(3);
                $entity = $repository->find($id);
            }
            if (!$entity instanceof CreditTransactionInterface) {
                continue;
            }

            $fromWallet = $entity->getFromWallet();
            $targetWallet = $entity->getTargetWallet();

//            if ($fromWallet->getOwner()->getId() == $targetWallet->getOwner()->getId()) {
//                continue;
//            }

            $detail = sprintf('Chuyển %s credit từ %s (ví %s) đến %s (ví %s), nội dung: %s', $entity->getAmount(),
                $fromWallet->getOwner()->getUsername(), $fromWallet->getName(),
                $targetWallet->getOwner()->getUsername(), $targetWallet->getName(),
                $entity->getDetail()
            );

            $this->createAlertForWallet($entity, $fromWallet, $detail);
            $this->createAlertForWallet($entity, $targetWallet, $detail);
        }
    }

    /**
     * @param CreditTransactionInterface $creditTransaction
     * @param WalletInterface $wallet
     * @param $detail
     */
    private function createAlertForWallet(CreditTransactionInterface $creditTransaction, WalletInterface $wallet, $detail)
    {
        $alert = $this->createAlert($creditTransaction);
        $alert->setAccount($wallet->getOwner());

        if (empty($alert->getDetail())) {
            $alert->setDetail($detail);
        }

        $this->em->persist($alert);
    }

    /**
     * @param $ids
     */
    private function createAlertForUpdateSaleCredit($ids)
    {
        $repository = $this->em->getRepository(\Biddy\Bundle\UserSystem\SaleBundle\Entity\User::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof SaleInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);
            $alert->setTargetType(AlertInterface::TARGET_TYPE_CREDIT);

            if (empty($alert->getDetail())) {
                $alert->setDetail('Cập nhật ví');
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForNewProduct($ids)
    {
        $repository = $this->em->getRepository(Product::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof ProductInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);

            if (empty($alert->getDetail())) {
                $alert->setDetail(sprintf('Tạo mới sản phẩm %s, chủ đề %s', $entity->getId(), $entity->getSubject()));
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForUpdateProduct($ids)
    {
        $repository = $this->em->getRepository(Product::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof ProductInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);

            if (empty($alert->getDetail())) {
                $alert->setDetail(sprintf('Cập nhật sản phẩm %s', $entity->getId()));
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForNewAccount($ids)
    {
        $repository = $this->em->getRepository(User::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof AccountInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);
            $alert->setTargetType(AlertInterface::TARGET_TYPE_PROFILE);
            if (empty($alert->getDetail())) {
                $alert->setDetail('Chào mừng tới Biddy. Tài khoản đã được tạo. Vui lòng kiểm tra email và xác nhận tài khoản qua email. Cảm ơn');
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForUpdateAccount($ids)
    {
        $repository = $this->em->getRepository(User::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof AccountInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);
            $alert->setTargetType(AlertInterface::TARGET_TYPE_PROFILE);

            if (empty($alert->getDetail())) {
                if (!$entity->isEnabled()) {
                    $alert->setDetail('Tài khoản bị khóa. Liên hệ ban quản trị để mở khóa');
                } else {
                    $alert->setDetail('Tài khoản được cập nhật');
                }
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForNewSale($ids)
    {
        $repository = $this->em->getRepository(\Biddy\Bundle\UserSystem\SaleBundle\Entity\User::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof SaleInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);
            $alert->setTargetType(AlertInterface::TARGET_TYPE_PROFILE);
            if (empty($alert->getDetail())) {
                $alert->setDetail('Chào mừng tới Biddy. Tài khoản đã được tạo. Vui lòng kiểm tra email và xác nhận tài khoản qua email. Cảm ơn');
            }

            $this->em->persist($alert);
        }
    }

    /**
     * @param $ids
     */
    private function createAlertForUpdateSale($ids)
    {
        $repository = $this->em->getRepository(\Biddy\Bundle\UserSystem\SaleBundle\Entity\User::class);

        foreach ($ids as $id) {
            $entity = $repository->find($id);
            if (!$entity instanceof SaleInterface) {
                continue;
            }

            $alert = $this->createAlert($entity);
            $alert->setTargetType(AlertInterface::TARGET_TYPE_PROFILE);

            if (empty($alert->getDetail())) {
                if (!$entity->isEnabled()) {
                    $alert->setDetail('Tài khoản bị khóa. Liên hệ ban quản trị để mở khóa');
                } else {
                    $alert->setDetail('Tài khoản được cập nhật');
                }
            }

            $this->em->persist($alert);
        }
    }
}