<?php

namespace Biddy\Repository\Core;


use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;
use Doctrine\Common\Persistence\ObjectRepository;

interface TaskRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getTasksForUserQuery(UserRoleInterface $user, PagerParam $param);
}