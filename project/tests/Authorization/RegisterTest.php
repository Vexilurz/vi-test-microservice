<?php

namespace App\Tests\Authorization;

use App\Entity\User;
use App\Tests\VitmWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegisterTest extends VitmWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/register');
        $this->setApiToken('');
    }

    public function testRegister(): void
    {
        $newEmail = 'newuser@example.com';
        $this->setBody(['email'=>$newEmail,'password'=>'123456']);
        $this->checkResponseWithApiToken('registration success');

        try {
            $this->getEntityManager()
                ->getRepository(User::class)
                ->deleteByEmail($newEmail);
        } catch(\Exception $e) {
            self::fail($e->getMessage());
        }
    }

    public function testRegisterExisting(): void
    {
        $this->setBody(['email'=>'test@example.com','password'=>'123456']);
        $this->setResponseCode(Response::HTTP_BAD_REQUEST);
        $this->checkResponseWithMessage('user already exists');
    }

    private function checkRegisterWithoutSomeData() {
        $this->setResponseCode(Response::HTTP_BAD_REQUEST);
        $this->checkResponseWithMessage('email or password is empty');
    }

    public function testRegisterWithoutEmail(): void
    {
        $this->setBody(['password'=>'123456']);
        $this->checkRegisterWithoutSomeData();
    }

    public function testRegisterWithoutPassword(): void
    {
        $this->setBody(['email'=>'newuser@example.com']);
        $this->checkRegisterWithoutSomeData();
    }

    public function testRegisterWithoutData(): void
    {
        $this->checkRegisterWithoutSomeData();
    }
}
