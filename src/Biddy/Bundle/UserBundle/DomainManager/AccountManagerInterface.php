<?php

namespace Biddy\Bundle\UserBundle\DomainManager;

use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use FOS\UserBundle\Model\UserInterface;
use Biddy\Model\Core\ReportViewTemplateInterface;
use Biddy\Model\User\UserEntityInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Exception\InvalidUserRoleException;

interface AccountManagerInterface
{
    /**
     * @see \Biddy\DomainManager\ManagerInterface
     *
     * @param FOSUserInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param FOSUserInterface $user
     * @return void
     */
    public function save(FOSUserInterface $user);

    /**
     * @param FOSUserInterface $user
     * @return void
     */
    public function delete(FOSUserInterface $user);

    /**
     * Create new Account only
     * @return FOSUserInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return FOSUserInterface|UserEntityInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return FOSUserInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @return array
     */
    public function allAccounts();

    /**
     * @return array
     */
    public function allActiveAccounts();

    /**
     * @param int $id
     * @return AccountInterface|bool
     * @throws InvalidUserRoleException
     */
    public function findAccount($id);

    /**
     * Finds a user by its username or email.
     *
     * @param string $usernameOrEmail
     *
     * @return UserInterface or null if user does not exist
     */
    public function findUserByUsernameOrEmail($usernameOrEmail);

    /**
     * Updates a user.
     *
     * @param UserInterface $token
     *
     * @return void
     */
    public function updateUser(UserInterface $token);

    /**
     * Finds a user by its confirmationToken.
     *
     * @param string $token
     *
     * @return UserInterface or null if user does not exist
     */
    public function findUserByConfirmationToken($token);

    public function updateCanonicalFields(UserInterface $user);

    public function generateUuid(UserInterface $user);
    
    /**
     * @param $phone
     * @return UserInterface
     */
    public function findUserByPhone($phone);

}