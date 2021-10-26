<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;

abstract class VitmAuthWebTestCase extends VitmBaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function checkResponseWithApiToken(string $receivedMessage = ''): void
    {
        $this->checkResponseWithMessage($receivedMessage);
        self::assertArrayHasKey('apiToken', $this->getResponseJson());
    }

    public function checkUnauthorized(string $receivedMessage = 'Invalid credentials.') {
        $this->setResponseCode(Response::HTTP_UNAUTHORIZED);
        $this->checkResponseWithMessage($receivedMessage);
    }
}