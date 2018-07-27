<?php

namespace Biddy\Worker\Job\Concurrent\Wallet;

use Biddy\DomainManager\WalletManagerInterface;
use Biddy\Model\Core\WalletInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Service\Util\WalletUtilTrait;
use Biddy\Worker\Core\Job\JobInterface;
use Biddy\Worker\Core\JobParams;
use Doctrine\Common\Collections\Collection;
use Monolog\Logger;

class CreateWalletsForUserWorker implements JobInterface
{
    use WalletUtilTrait;

    const JOB_NAME = 'CreateWalletsForUserWorker';
    const PARAM_KEY_USER_IDS = 'user_ids';
    const PARAM_KEY_CONTEXT = 'context';

    /** @var Logger $logger */
    private $logger;

    /** @var WalletManagerInterface */
    private $walletManager;

    private $userManagers;

    /**
     * CreateWalletsForUserWorker constructor.
     * @param Logger $logger
     * @param WalletManagerInterface $walletManager
     * @param $userManagers
     */
    public function __construct(Logger $logger, WalletManagerInterface $walletManager, $userManagers)
    {
        $this->logger = $logger;
        $this->walletManager = $walletManager;
        $this->userManagers = $userManagers;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::JOB_NAME;
    }

    /**
     * @inheritdoc
     */
    public function run(JobParams $params)
    {
        $userIds = $params->getRequiredParam(self::PARAM_KEY_USER_IDS);
        $userIds = is_array($userIds) ? $userIds : [$userIds];

        if (empty($userIds)) {
            return;
        }

        foreach ($userIds as $id) {
            $user = $this->findUserById($id);
            if (!$user instanceof UserRoleInterface) {
                continue;
            }

            $this->createWalletsForUser($user);
        }
    }

    /**
     * @param $id
     * @return null
     */
    private function findUserById($id)
    {
        foreach ($this->userManagers as $userManager) {
            try {
                $user = $userManager->find($id);
                if ($user instanceof UserRoleInterface) {
                    return $user;
                }
            } catch (\Exception $e) {

            }
        }

        return null;
    }

    /**
     * @param UserRoleInterface $user
     */
    private function createWalletsForUser(UserRoleInterface $user)
    {
        $existedWalletNames = $this->getCurrentWalletsName($user);

        $walletNames = WalletInterface::SUPPORT_WALLETS;
        foreach ($walletNames as $name) {
            if (in_array($name, $existedWalletNames)) {
                continue;
            }

            $wallet = $this->createWallet($user, $name);
            $this->walletManager->save($wallet);
        }
    }

    /**
     * @param $user
     * @return array
     */
    private function getCurrentWalletsName(UserRoleInterface $user)
    {
        $wallets = $user->getWallets();
        $wallets = $wallets instanceof Collection ? $wallets->toArray() : $wallets;

        $walletNames = array_map(function ($wallet) {
            if ($wallet instanceof WalletInterface) {
                return $wallet->getType();
            }
        }, $wallets);

        return array_unique($walletNames);
    }
}