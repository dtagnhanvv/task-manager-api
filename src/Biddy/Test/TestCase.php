<?php

namespace Biddy\Test;

use Biddy\Model\User\UserEntityInterface;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $roles
     * @return UserEntityInterface
     */
    protected function getUser(array $roles)
    {
        $user = $this->getMock('Biddy\Model\User\UserEntityInterface');

        $user->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue($roles));

        $user->expects($this->any())
            ->method('hasRole')
            ->will($this->returnCallback(function($subject) use($roles) {
                return(in_array($subject, $roles));
            }));
        ;

        return $user;
    }
}