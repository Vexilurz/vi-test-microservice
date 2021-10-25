<?php

namespace App\Tests\Authorization;

use App\Tests\Utils\TestUtils;
use App\Tests\VitmWebTestCase;

class LogoutTest extends VitmWebTestCase
{
    public function testLogout(): void
    {
        $client = static::createClient();
        $client->request('POST', '/logout', [], [], [
            'HTTP_X-AUTH-TOKEN'=>'logout_token'
        ]);

        self::assertResponseIsSuccessful();
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('message', $responseData);
        self::assertSame('logout success', $responseData['message']);
        self::assertArrayNotHasKey('apiToken', $responseData);
    }

    public function testLogoutWithBadToken(): void
    {
        $client = static::createClient();
        $client->request('POST', '/logout', [], [], [
            'HTTP_X-AUTH-TOKEN'=>TestUtils::getRandomStr()
        ]);

        $this->checkUnauthorized($client);
    }

    public function testLogoutWithoutToken(): void
    {
        $client = static::createClient();
        $client->request('POST', '/logout');

        $this->checkUnauthorized($client, 'No API token provided');
    }
}
