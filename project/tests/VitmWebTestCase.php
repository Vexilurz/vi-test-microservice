<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class VitmWebTestCase extends WebTestCase
{
    protected function checkRequest(string $method = 'POST', string $url = '/', array $body = [],
                                    int $responseCode = 200, string $receivedMessage = '',
                                    bool $apiTokenExist = false) {
        $client = static::createClient();
        $client->request($method, $url, $body);
        self::assertResponseStatusCodeSame($responseCode);
        if ($receivedMessage) {
            $response = $client->getResponse();
            $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
            self::assertArrayHasKey('message', $responseData);
            self::assertSame($receivedMessage, $responseData['message']);
            self::assertSame($apiTokenExist, array_key_exists('apiToken', $responseData));
        }
    }

    protected function checkUnauthorized(string $method = 'POST', string $url = '/', array $body = []) {
        $this->checkRequest($method, $url, $body,
            Response::HTTP_UNAUTHORIZED, 'Invalid credentials.', false);
    }
}