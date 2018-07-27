<?php


namespace Biddy\Service\Util;

use Biddy\Entity\Core\Wallet;
use Biddy\Model\Core\WalletInterface;
use Biddy\Model\User\Role\UserRoleInterface;

trait WalletUtilTrait
{
    /**
     * @param UserRoleInterface $owner
     * @param $type
     * @return Wallet
     */
    public function createWallet(UserRoleInterface $owner, $type)
    {
        $wallet = new Wallet();
        $wallet->setOwner($owner);
        $wallet->setType($type);
        $wallet->setName($this->getWalletName($type));

        return $wallet;
    }

    /**
     * @param $wallets
     * @return array
     */
    public function serializeWallets($wallets)
    {
        $groups = [];
        foreach ($wallets as $wallet) {
            if (!$wallet instanceof WalletInterface) {
                continue;
            }

            $group = [];
            $group['id'] = $wallet->getId();
            $group['owner'] = $wallet->getOwner();
            $group['credit'] = $wallet->getCredit();
            $group['previousCredit'] = $wallet->getPreviousCredit();
            $group['createdDate'] = $wallet->getCreatedDate();
            $group['expiredAt'] = $wallet->getExpiredAt();
            $group['type'] = $wallet->getType();
            $group['name'] = $wallet->getName();

            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * @param $wallet
     * @return array
     */
    public function serializeSingWallet($wallet)
    {
        if (!$wallet instanceof WalletInterface) {
            return [];
        }

        $group['id'] = $wallet->getId();
        $group['name'] = $wallet->getName();
        $group['owner'] = $wallet->getOwner()->getUsername();

        return $group;
    }
    
    /**
     * @param $type
     * @return string
     */
    public function getWalletName($type)
    {
        $name = '';
        switch ($type) {
            case WalletInterface::TYPE_BASIC:
                $name = 'Tài khoản thanh toán';
                break;
            case WalletInterface::TYPE_INSURE:
                $name = 'Tài khoản đảm bảo';
                break;
            case WalletInterface::TYPE_FEE:
                $name = 'Phí đã trả';
                break;
        }

        return $name;
    }
}