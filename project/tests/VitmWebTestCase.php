<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class VitmWebTestCase extends WebTestCase
{
    protected function checkUnauthorized($client, $message = 'Invalid credentials.') {
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('message', $responseData);
        self::assertSame($message, $responseData['message']);
        self::assertArrayNotHasKey('apiToken', $responseData);
    }
}