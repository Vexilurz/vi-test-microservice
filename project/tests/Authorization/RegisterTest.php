<?php

namespace App\Tests\Authorization;

use App\Tests\Utils\TestUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegisterTest extends WebTestCase
{
    public function testRegister(): void
    {
        $newEmail = TestUtils::getRandomStr().'@example.com';
        $client = static::createClient();
        $client->request('POST', '/register',
            ['email'=>$newEmail,'password'=>'123456']);

        self::assertResponseIsSuccessful();
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('message', $responseData);
        self::assertSame('registration success', $responseData['message']);
        self::assertArrayHasKey('apiToken', $responseData);

//        $client->getContainer()
//            ->get('doctrine.orm.entity_manager')
//            ->getRepository(User::class)
//            ->deleteByEmail($newEmail);
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
            ['email'=>TestUtils::getRandomStr().'@example.com']);
        $this->checkRegisterWithoutData($client);
    }

    public function testRegisterWithoutData(): void
    {
        $client = static::createClient();
        $client->request('POST', '/register');
        $this->checkRegisterWithoutData($client);
    }
}
