<?php

namespace App\Tests\Authorization;

use App\Tests\VitmWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends VitmWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/login');
        $this->setApiToken('');
    }

    public function testLogin(): void
    {
        $this->setBody(['email'=>'user@example.com','password'=>'123456']);
        $this->checkResponseWithApiToken('login success');
    }

    public function testLoginNonExistentEmail(): void
    {
        $this->setBody(['email'=>'non-exixtent@example.com','password'=>'123456']);
        $this->checkUnauthorized();
    }

    public function testLoginWithoutEmail(): void
    {
        $this->setBody(['password'=>'123456']);
        $this->checkUnauthorized();
    }

    public function testLoginWithoutPassword(): void
    {
        $this->setBody(['email'=>'user@example.com']);
        $this->checkUnauthorized();
    }

    public function testLoginWithIncorrectPassword(): void
    {
        $this->setBody(['email'=>'user@example.com','password'=>'my_incorrect_password']);
        $this->checkUnauthorized();
    }

    public function testLoginWithoutUserInformation(): void
    {
        $this->checkUnauthorized();
    }

//    public function testLoginWithNotPostMethod(): void
//    {
//        $this->setMethod('GET');
//        $this->setResponseCode(Response::HTTP_METHOD_NOT_ALLOWED);
//        $this->checkResponse(false);
//    }
}
