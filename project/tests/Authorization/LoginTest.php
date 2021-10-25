<?php

namespace App\Tests\Authorization;

use App\Tests\Utils\TestUtils;
use App\Tests\VitmWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends VitmWebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login',
            ['email'=>'user@example.com','password'=>'123456']);

        self::assertResponseIsSuccessful();
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('message', $responseData);
        self::assertSame('login success', $responseData['message']);
        self::assertArrayHasKey('apiToken', $responseData);
    }

    public function testLoginNonExistentEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login',
            ['email'=>TestUtils::getRandomStr().'@example.com','password'=>'123456']);
        $this->checkUnauthorized($client);
    }

    public function testLoginWithoutEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login',
            ['password'=>'123456']);
        $this->checkUnauthorized($client);
    }

    public function testLoginWithoutPassword(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login',
            ['email'=>'user@example.com']);
        $this->checkUnauthorized($client);
    }

    public function testLoginWithIncorrectPassword(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login',
            ['email'=>'user@example.com','password'=>'my_incorrect_password']);
        $this->checkUnauthorized($client);
    }

    public function testLoginWithoutUserInformation(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login');
        $this->checkUnauthorized($client);
    }

    public function testLoginWithNotPostMethod(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
