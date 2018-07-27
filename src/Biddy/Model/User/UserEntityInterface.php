<?php

namespace Biddy\Model\User;

use Biddy\Model\Core\WalletInterface;
use Biddy\Model\ModelInterface;

interface UserEntityInterface extends ModelInterface
{
    public function getId();

    public function getUsername();

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * @return []
     */
    public function getRoles();

    public function hasRole($role);

    /**
     * Adds a role to the user.
     *
     * @param string $role
     *
     * @return self
     */
    public function addRole($role);

    /**
     * @param array $modules
     * @return void
     */
    public function setEnabledModules(array $modules);

    /**
     * @param array $roles
     * @return void
     */
    public function setUserRoles(array $roles);

    /**
     * @return array
     */
    public function getEnabledModules();

    /**
     * @return bool;
     */
    public function hasProductModule();

    /**
     * @return bool
     */
    public function hasBiddingModule();

    /**
     * @return bool
     */
    public function hasCommentModule();

    /**
     * @return bool
     */
    public function hasCreditModule();

    /**
     * @return array
     */
    public function getUserRoles();

    public function isEnabled();

    public function getType();

    public function setType($type);


    /**
     * @return boolean
     */
    public function isTestAccount();

    /**
     * @param boolean $testAccount
     * @return self
     */
    public function setTestAccount($testAccount);

    /**
     * @return \Biddy\Model\Core\WalletInterface[]
     * @return self
     */
    public function getWallets();

    /**
     * @param \Biddy\Model\Core\WalletInterface[] $wallets
     * @return self
     */
    public function setWallets($wallets);

    /**
     * @return WalletInterface
     */
    public function getBasicWallet();

    /**
     * @return WalletInterface
     */
    public function getInsureWallet();

    /**
     * @return mixed
     */
    public function getFeeWallet();
    
    /**
     * @return mixed
     */
    public function getProfileImageUrl();

    /**
     * @param mixed $profileImageUrl
     * @return self
     */
    public function setProfileImageUrl($profileImageUrl);

    /**
     * @return mixed
     */
    public function getUserPreferences();

    /**
     * @param mixed $userPreferences
     */
    public function setUserPreferences($userPreferences);
}