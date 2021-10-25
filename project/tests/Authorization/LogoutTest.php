<?php

namespace App\Tests\Authorization;

use App\Tests\VitmWebTestCase;

class LogoutTest extends VitmWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/logout');
        $this->setApiToken('');
    }

    public function testLogout(): void
    {
        $this->setUrl('/login');
        $this->setBody(['email'=>'user@example.com','password'=>'123456']);
        $this->checkResponseWithApiToken('login success');

        $this->setUrl('/logout');
        $this->setApiToken($this->getResponseJson()['apiToken']);
        $this->checkResponseWithMessage('logout success');
    }

    public function testLogoutWithBadToken(): void
    {
        $this->setApiToken('bad_token');
        $this->checkUnauthorized();
    }

    public function testLogoutWithoutToken(): void
    {
        $this->checkUnauthorized('No API token provided');
    }
}
