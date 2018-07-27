<?php

namespace Biddy\Bundle\ApiBundle\Service;

use Biddy\Model\Core\WalletInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\UserEntityInterface;

class JWTResponseTransformer
{
    public function transform(array $data, UserEntityInterface $user)
    {
        $data['id'] = $user->getId();
        $data['username'] = $user->getUsername();
        $data['userRoles'] = $user->getUserRoles();
        $data['enabledModules'] = $user->getEnabledModules();
        $data['avatarUrl'] = $user->getProfileImageUrl();
        $data['userPreferences'] = $user->getUserPreferences();

        $basicWallet = $user->getBasicWallet();
        if ($basicWallet instanceof WalletInterface) {
            $data['basicWallet']['id'] = $basicWallet->getId();
            $data['basicWallet']['credit'] = $basicWallet->getCredit();
        }

        if ($user instanceof AccountInterface) {
            $data['settings'] = $user->getSettings();
            $data['exchanges'] = $user->getExchanges();
        }

        return $data;
    }
}