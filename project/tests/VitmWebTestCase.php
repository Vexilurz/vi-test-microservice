<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class VitmWebTestCase extends WebTestCase
{
    private string $_method;
    private string $_url;
    private array $_body;
    private int $_responseCode;
    private string $_apiToken;
    private $_responseJson = [];
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $_client;
    private $_em;

    public function setUp(): void
    {
        $this->setDefaults();
        $this->_client = static::createClient();
        $this->_em = $this->_client->getContainer()->get('doctrine.orm.entity_manager');
    }

    private function setDefaults(): void {
        $this->_method = 'GET';
        $this->_url = '/';
        $this->_body = [];
        $this->_responseCode = Response::HTTP_OK;
        $this->_apiToken = '';
    }

    public function setMethod(string $method): void
    {
        $this->_method = $method;
    }

    public function setUrl(string $url): void
    {
        $this->_url = $url;
    }

    public function setBody(array $body): void
    {
        $this->_body = $body;
    }

    public function setResponseCode(int $responseCode): void
    {
        $this->_responseCode = $responseCode;
    }

    public function setApiToken(string $apiToken): void
    {
        $this->_apiToken = $apiToken;
    }

    private function getHeaders(): array {
        $headers = [];
        if ($this->_apiToken) {
            $headers['HTTP_X-AUTH-TOKEN'] = $this->_apiToken;
        }
        return $headers;
    }

    public function getResponseJson() {
        return $this->_responseJson;
    }

    public function getEntityManager() {
        return $this->_em;
    }

    public function checkResponse($decodeJson = true): void
    {
        $this->_client->request($this->_method, $this->_url, $this->_body, [], $this->getHeaders());
        self::assertResponseStatusCodeSame($this->_responseCode);
        $response = $this->_client->getResponse();
        try {
             $this->_responseJson = !$decodeJson ? [] :
                 json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch(\Exception $e) {
            self::fail($e->getMessage());
        }
    }

    public function checkResponseWithMessage(string $receivedMessage = '', bool $apiTokenMustExist = false): void
    {
        $this->checkResponse();
        if ($receivedMessage) {
            self::assertArrayHasKey('message', $this->_responseJson);
            self::assertSame($receivedMessage, $this->_responseJson['message']);
            self::assertSame($apiTokenMustExist, array_key_exists('apiToken', $this->_responseJson));
        }
    }

    public function checkUnauthorized(string $receivedMessage = 'Invalid credentials.') {
        $this->setResponseCode(Response::HTTP_UNAUTHORIZED);
        $this->checkResponseWithMessage($receivedMessage);
    }
}