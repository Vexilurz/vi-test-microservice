<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthTest extends WebTestCase
{
    private function getRandomStr(): string {
        return md5(microtime());
    }

    private function checkUnauthorized($client) {
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('message', $responseData);
        self::assertSame('Invalid credentials.', $responseData['message']);
        self::assertArrayNotHasKey('apiToken', $responseData);
    }

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
            ['email'=>$this->getRandomStr().'@example.com','password'=>'123456']);
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

    public function testRegister(): void
    {
        $client = static::createClient();
        $client->request('POST', '/register',
            ['email'=>$this->getRandomStr().'@example.com','password'=>'123456']);

        self::assertResponseIsSuccessful();
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('message', $responseData);
        self::assertSame('registration success', $responseData['message']);
        self::assertArrayHasKey('apiToken', $responseData);
    }

    public function testRegisterExisting(): void
    {
        $client = static::createClient();
        $client->request('POST', '/register',
            ['email'=>'test@example.com','password'=>'123456']);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('message', $responseData);
        self::assertSame('user already exists', $responseData['message']);
        self::assertArrayNotHasKey('apiToken', $responseData);
    }

    private function checkRegisterWithoutData($client) {
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('message', $responseData);
        self::assertSame('email or password is empty', $responseData['message']);
        self::assertArrayNotHasKey('apiToken', $responseData);
    }

    public function testRegisterWithoutEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/register',
            ['password'=>'123456']);
        $this->checkRegisterWithoutData($client);
    }

    public function testRegisterWithoutPassword(): void
    {
        $client = static::createClient();
        $client->request('POST', '/register',
            ['email'=>$this->getRandomStr().'@example.com']);
        $this->checkRegisterWithoutData($client);
    }

    public function testRegisterWithoutData(): void
    {
        $client = static::createClient();
        $client->request('POST', '/register');
        $this->checkRegisterWithoutData($client);
    }
}
