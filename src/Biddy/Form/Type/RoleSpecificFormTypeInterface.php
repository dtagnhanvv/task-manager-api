<?php

namespace Biddy\Form\Type;

use Symfony\Component\Form\FormTypeInterface;
use Biddy\Model\User\Role\UserRoleInterface;

interface RoleSpecificFormTypeInterface extends FormTypeInterface
{
    public function setUserRole(UserRoleInterface $userRole);
}