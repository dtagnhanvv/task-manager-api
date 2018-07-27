<?php

namespace Biddy\Bundle\UserBundle\DomainManager;

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use Biddy\Bundle\UserSystem\AccountBundle\Entity\User;
use Biddy\DomainManager\UserTagManagerInterface;
use Biddy\Exception\LogicException;
use Biddy\Model\Core\ReportViewTemplateInterface;
use Biddy\Model\Core\ReportViewTemplateTagInterface;
use Biddy\Model\Core\UserTagInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\UserEntityInterface;

/**
 * Most of the other handlers talk to doctrine directly
 * This one is wrapping the bundle-specific FOSUserBundle
 * whilst keep a consistent API with the other handlers
 */
class AccountManager implements AccountManagerInterface
{
    const ROLE_ACCOUNT = 'ROLE_ACCOUNT';
    const ROLE_ADMIN = 'ROLE_ADMIN';

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
    public function allAccounts()
    {
        $accounts = array_filter($this->all(), function(UserEntityInterface $user) {
            return $user->hasRole(static::ROLE_ACCOUNT);
        });

        return array_values($accounts);
    }

    /**
     * @return array
     */
    public function allActiveAccounts()
    {
        $accounts = array_filter($this->all(), function(UserEntityInterface $user) {
            return $user->hasRole(static::ROLE_ACCOUNT) && $user->isEnabled();
        });

        return array_values($accounts);
    }

    /**
     * @inheritdoc
     */
    public function findAccount($id)
    {
        $account = $this->find($id);

        if (!$account) {
            return false;
        }

        if (!$account instanceof AccountInterface) {
            return false;
        }

        return $account;
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

    /**
     * @inheritdoc
     */
    public function findUserByPhone($phone)
    {
        return $this->FOSUserManager->findUserBy(array('phone' => $phone));
    }
}