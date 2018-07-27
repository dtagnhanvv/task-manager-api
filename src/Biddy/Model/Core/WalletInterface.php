<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\UserRoleInterface;

interface WalletInterface extends ModelInterface
{
    const TYPE_BASIC = 'basic';
    const TYPE_INSURE = 'insure';
    const TYPE_FEE = 'fee';

    const SUPPORT_WALLETS = [
        self::TYPE_BASIC,
        self::TYPE_INSURE,
        self::TYPE_FEE,
    ];

    /**
     * @param $deletedAt
     * @return self
     */
    public function setDeletedAt($deletedAt);

    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @return mixed
     */
    public function getCreatedDate();

    /**
     * @param mixed $createdDate
     * @return ProductInterface
     */
    public function setCreatedDate($createdDate);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);
    
    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param mixed $type
     * @return self
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getCurrency();

    /**
     * @param mixed $currency
     * @return self
     */
    public function setCurrency($currency);

    /**
     * @return mixed
     */
    public function getValidFrom();

    /**
     * @param mixed $validFrom
     * @return self
     */
    public function setValidFrom($validFrom);

    /**
     * @return mixed
     */
    public function getExpiredAt();

    /**
     * @param mixed $expiredAt
     * @return self
     */
    public function setExpiredAt($expiredAt);

    /**
     * @return mixed
     */
    public function getCredit();

    /**
     * @param mixed $credit
     * @return self
     */
    public function setCredit($credit);

    /**
     * @return mixed
     */
    public function getPreviousCredit();

    /**
     * @param mixed $previousCredit
     * @return self
     */
    public function setPreviousCredit($previousCredit);

    /**
     * @return UserRoleInterface
     */
    public function getOwner();

    /**
     * @param UserRoleInterface $owner
     * @return self
     */
    public function setOwner($owner);

    /**
     * @return CreditTransactionInterface[]
     */
    public function getCreditTransactions();

    /**
     * @param CreditTransactionInterface[] $creditTransactions
     * @return self
     */
    public function setCreditTransactions($creditTransactions);
}