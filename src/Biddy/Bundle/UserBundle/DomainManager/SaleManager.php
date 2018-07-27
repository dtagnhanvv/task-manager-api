<?php

namespace Biddy\Bundle\UserBundle\DomainManager;

use Biddy\Exception\LogicException;
use Biddy\Model\User\Role\SaleInterface;
use Biddy\Model\User\UserEntityInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;

/**
 * Most of the other handlers talk to doctrine directly
 * This one is wrapping the bundle-specific FOSUserBundle
 * whilst keep a consistent API with the other handlers
 */
class SaleManager implements SaleManagerInterface
{
    const ROLE_SALE = 'ROLE_SALE';

    /**
     * @var UserManagerInterface
     */
    protected $FOSUserManager;

    protected $userTagManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->FOSUserManager = $userManager;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, FOSUserInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(FOSUserInterface $user)
    {
        $this->FOSUserManager->updateUser($user);
    }

    /**
     * @inheritdoc
     */
    public function delete(FOSUserInterface $user)
    {
        $this->FOSUserManager->deleteUser($user);
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        return $this->FOSUserManager->createUser();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->FOSUserManager->findUserBy(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->FOSUserManager->findUsers();
    }

    /**
     * @inheritdoc
     */
    public function allSales()
    {
        $sales = array_filter($this->all(), function(UserEntityInterface $user) {
            return $user->hasRole(static::ROLE_SALE);
        });

        return array_values($sales);
    }

    /**
     * @return array
     */
    public function allActiveSales()
    {
        $sales = array_filter($this->all(), function(UserEntityInterface $user) {
            return $user->hasRole(static::ROLE_SALE) && $user->isEnabled();
        });

        return array_values($sales);
    }

    /**
     * @inheritdoc
     */
    public function findSale($id)
    {
        $sale = $this->find($id);

        if (!$sale) {
            return false;
        }

        if (!$sale instanceof SaleInterface) {
            return false;
        }

        return $sale;
    }

    /**
     * @inheritdoc
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        return $this->FOSUserManager->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * @inheritdoc
     */
    public function updateUser(UserInterface $token)
    {
        $this->FOSUserManager->updateUser($token);
    }

    /**
     * @inheritdoc
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->FOSUserManager->findUserByConfirmationToken($token);
    }

    public function updateCanonicalFields(UserInterface $user)
    {
        $this->FOSUserManager->updateCanonicalFields($user);
    }

    public function generateUuid(UserInterface $user)
    {
        try {
            $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $user->getEmail());
            return $uuid5->toString();

        } catch(UnsatisfiedDependencyException $e) {
            throw new LogicException($e->getMessage());
        }
    }
}