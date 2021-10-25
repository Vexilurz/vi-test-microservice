<?php

namespace App\Tests\Authorization;

use App\Tests\Utils\TestUtils;
use App\Tests\VitmWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends VitmWebTestCase
{
    public function testLogin(): void
    {
        $this->checkRequest('POST', '/login', ['email'=>'user@example.com','password'=>'123456'],
        Response::HTTP_OK, 'login success', true);
    }

    public function testLoginNonExistentEmail(): void
    {
        $this->checkUnauthorized('POST', '/login',
            ['email'=>TestUtils::getRandomStr().'@example.com','password'=>'123456']);
    }

    public function testLoginWithoutEmail(): void
    {
        $this->checkUnauthorized('POST', '/login',
            ['password'=>'123456']);
    }

    public function testLoginWithoutPassword(): void
    {
        $this->checkUnauthorized('POST', '/login',
            ['email'=>'user@example.com']);
    }

    public function testLoginWithIncorrectPassword(): void
    {
        $this->checkUnauthorized('POST', '/login',
            ['email'=>'user@example.com','password'=>'my_incorrect_password']);
    }

    public function testLoginWithoutUserInformation(): void
    {
        $this->checkUnauthorized('POST', '/login');
    }

    public function testLoginWithNotPostMethod(): void
    {
        $this->checkRequest('GET', '/login', [], Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
