<?php

namespace App\Tests\Authorization;

use App\Tests\VitmAuthWebTestCase;

class LogoutTest extends VitmAuthWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/logout');
    }

    public function testLogout(): void
    {
        $this->setUrl('/logout');
        $this->checkResponseWithMessage('logout success');
    }

    public function testLogoutWithBadToken(): void
    {
        $this->setApiToken('bad_token');
        $this->checkUnauthorized();
    }

    public function testLogoutWithoutToken(): void
    {
        $this->setApiToken('');
        $this->checkUnauthorized('No API token provided');
    }
}
