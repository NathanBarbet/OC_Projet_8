<?php

namespace tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{


    public function testUsername()
    {
        $user = new User();
        $user->setUsername("username");
        $this->assertEquals("username", $user->getUsername());
    }

    public function testGetSalt()
    {
        $user = new User();
        $this->assertSame(null, $user->getSalt());
    }

    public function testGetpassword()
    {
        $user = new User();
        $user->setPassword('password');
        $this->assertSame('password', $user->getPassword());
    }

    public function testGetEmail()
    {
        $user = new User();
        $user->setEmail('email');
        $this->assertSame('email', $user->getEmail());
    }

    public function testGetRoles()
    {
        $user = new User();
        $roles = ['ROLE_USER'];
        $user->setRoles($roles);
        $this->assertSame($roles, $user->getRoles());
    }
}
