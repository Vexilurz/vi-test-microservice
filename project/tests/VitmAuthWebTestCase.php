<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;

abstract class VitmAuthWebTestCase extends VitmBaseWebTestCase
{
    public function checkUnauthorized(string $receivedMessage = 'Invalid credentials.') {
        $this->setResponseCode(Response::HTTP_UNAUTHORIZED);
        $this->checkResponseWithMessage($receivedMessage);
    }
}