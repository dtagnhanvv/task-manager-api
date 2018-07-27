<?php

namespace Biddy\Bundle\UserBundle\Entity;

use Biddy\Model\Core\WalletInterface;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\User\UserEntityInterface;

abstract class User extends BaseUser implements UserEntityInterface
{
    const USER_ROLE_PREFIX = 'ROLE_';
    const MODULE_PREFIX = 'MODULE_';

    const MODULE_USER = 'MODULE_USER';
    const MODULE_PRODUCT = 'MODULE_PRODUCT';
    const MODULE_COMMENT = 'MODULE_COMMENT';
    const MODULE_BIDDING = 'MODULE_BIDDING'; //source
    const MODULE_CREDIT = 'MODULE_CREDIT'; //source
    const DEFAULT_MODULES_FOR_ACCOUNT = [
        self::MODULE_PRODUCT => self::MODULE_PRODUCT,
        self::MODULE_COMMENT => self::MODULE_COMMENT,
        self::MODULE_BIDDING => self::MODULE_BIDDING,
        self::MODULE_CREDIT => self::MODULE_CREDIT
    ];
    const DEFAULT_MODULES_FOR_SALE = [
        self::MODULE_USER => self::MODULE_USER,
        self::MODULE_CREDIT => self::MODULE_CREDIT
    ];
    // we have to redefine the properties we wish to expose with JMS Serializer Bundle

    protected $id;
    protected $username;
    protected $email;
    protected $enabled;
    protected $lastLogin;
    protected $roles;
    protected $joinDate;

    protected $type;
    protected $testAccount = false;
    protected $profileImageUrl;
    protected $userPreferences;

    /** @var  WalletInterface[] */
    protected $wallets;

    /**
     * @inheritdoc
     */
    public function hasProductModule()
    {
        return in_array(static::MODULE_PRODUCT, $this->getEnabledModules());
    }

    /**
     * @inheritdoc
     */
    public function hasBiddingModule()
    {
        return in_array(static::MODULE_BIDDING, $this->getEnabledModules());
    }

    /**
     * @return bool
     */
    public function hasCreditModule()
    {
        return in_array(static::MODULE_CREDIT, $this->getEnabledModules());
    }

    /**
     * @return bool
     */
    public function hasCommentModule()
    {
        return in_array(static::MODULE_COMMENT, $this->getEnabledModules());
    }

    /**
     * @inheritdoc
     */
    public function setEnabledModules(array $modules)
    {
        $this->replaceRoles(
            $this->getEnabledModules(), // old roles
            $modules, // new roles
            static::MODULE_PREFIX,
            $strict = false // this means we add the role prefix and convert to uppercase if it does not exist
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setUserRoles(array $roles)
    {
        $this->replaceRoles(
            $this->getUserRoles(), // old roles
            $roles, // new roles
            static::USER_ROLE_PREFIX,
            $strict = false // this means we add the role prefix and convert to uppercase if it does not exist
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEnabledModules()
    {
        return $this->getRolesWithPrefix(static::MODULE_PREFIX);
    }

    /**
     * @inheritdoc
     */
    public function getUserRoles()
    {
        $roles = $this->getRolesWithPrefix(static::USER_ROLE_PREFIX);

        $roles = array_filter($roles, function ($role) {
            return $role !== static::ROLE_DEFAULT;
        });

        return $roles;
    }

    public function setEmail($email)
    {
        if (empty($email)) {
            $email = null;
        }

        $this->email = $email;

        return $this;
    }

    public function setEmailCanonical($emailCanonical)
    {
        if (empty($emailCanonical)) {
            $emailCanonical = null;
        }

        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * @param string $prefix i.e ROLE_ or FEATURE_
     * @return array
     */
    protected function getRolesWithPrefix($prefix)
    {
        $roles = array_filter($this->getRoles(), function ($role) use ($prefix) {
            return $this->checkRoleHasPrefix($role, $prefix);
        });

        return array_values($roles);
    }

    protected function checkRoleHasPrefix($role, $prefix)
    {
        return strpos($role, $prefix) === 0;
    }

    protected function addRoles(array $roles)
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    protected function removeRoles(array $roles)
    {
        foreach ($roles as $role) {
            $this->removeRole($role);
        }
    }

    /**
     * @return boolean
     */
    public function isTestAccount()
    {
        return $this->testAccount;
    }

    /**
     * @param boolean $testAccount
     * @return $this|\Biddy\Model\User\UserEntityInterface
     */
    public function setTestAccount($testAccount)
    {
        $this->testAccount = $testAccount;

        return $this;
    }

    /**
     * @param array $oldRoles
     * @param array $newRoles
     * @param $requiredRolePrefix
     * @param bool $strict ensure that the roles have the prefix, don't try to add it
     */
    protected function replaceRoles(array $oldRoles, array $newRoles, $requiredRolePrefix, $strict = false)
    {
        $this->removeRoles($oldRoles);

        foreach ($newRoles as $role) {
            // converts fraud_detection to FEATURE_FRAUD_DETECTION
            if (!$this->checkRoleHasPrefix($role, $requiredRolePrefix)) {
                if ($strict) {
                    throw new InvalidArgumentException("role '%s' does not have the required prefix '%s'", $role, $requiredRolePrefix);
                }

                $role = $requiredRolePrefix . strtoupper($role);
            }

            $this->addRole($role);
        }
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getProfileImageUrl()
    {
        return $this->profileImageUrl;
    }

    /**
     * @inheritdoc
     */
    public function setProfileImageUrl($profileImageUrl)
    {
        $this->profileImageUrl = $profileImageUrl;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWallets()
    {
        return $this->wallets;
    }

    /**
     * @inheritdoc
     */
    public function setWallets($wallets)
    {
        $this->wallets = $wallets;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBasicWallet()
    {
        $wallets = $this->getWallets();
        $wallets = $wallets instanceof Collection ? $wallets->toArray() : $wallets;
        $wallets = is_array($wallets) ? $wallets : [$wallets];

        $basicWallets = array_filter($wallets, function ($item) {
            return $item instanceof WalletInterface && $item->getType() == WalletInterface::TYPE_BASIC;
        });

        return reset($basicWallets);
    }

    /**
     * @inheritdoc
     */
    public function getInsureWallet()
    {
        $wallets = $this->getWallets();
        $wallets = $wallets instanceof Collection ? $wallets->toArray() : $wallets;
        $wallets = is_array($wallets) ? $wallets : [$wallets];

        $insureWallets = array_filter($wallets, function ($item) {
            return $item instanceof WalletInterface && $item->getType() == WalletInterface::TYPE_INSURE;
        });

        return reset($insureWallets);
    }

    /**
     * @inheritdoc
     */
    public function getFeeWallet() {
        $wallets = $this->getWallets();
        $wallets = $wallets instanceof Collection ? $wallets->toArray() : $wallets;
        $wallets = is_array($wallets) ? $wallets : [$wallets];

        $historyWallets = array_filter($wallets, function ($item) {
            return $item instanceof WalletInterface && $item->getType() == WalletInterface::TYPE_FEE;
        });

        return reset($historyWallets);
    }

    /**
     * @inheritdoc
     */
    public function getCredit() {
        $basicWallet = $this->getBasicWallet();
        if (!$basicWallet instanceof WalletInterface) {
            return 0;
        }

        return $basicWallet->getCredit();
    }

    /**
     * @inheritdoc
     */
    public function getInsureCredit() {
        $insureWallet = $this->getInsureWallet();
        if (!$insureWallet instanceof WalletInterface) {
            return 0;
        }

        return $insureWallet->getCredit();
    }

    /**
     * @inheritdoc
     */
    public function getFeeCredit() {
        $feeWallet = $this->getFeeWallet();
        if (!$feeWallet instanceof WalletInterface) {
            return 0;
        }

        return $feeWallet->getCredit();
    }

    /**
     * @inheritdoc
     */
    public function getUserPreferences()
    {
        return $this->userPreferences;
    }

    /**
     * @inheritdoc
     */
    public function setUserPreferences($userPreferences)
    {
        $this->userPreferences = $userPreferences;

        return $this;
    }
}