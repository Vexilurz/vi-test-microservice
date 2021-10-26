<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class VitmBaseWebTestCase extends WebTestCase
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
        $this->_apiToken = 'test_token';
    }

    public function setNotOwnerApiToken() {
        $this->_apiToken = 'test_token2';
    }

    public function getDebugString(): string {
        return "$this->_method $this->_responseCode $this->_url".
            "\n\rbody: ".json_encode($this->_body).
            "\r\napiToken: $this->_apiToken";
    }

    public function setMethod(string $method): void
    {
        $this->_method = $method;
    }

    public function setUrl(string $url): void
    {
        $this->_url = $url;
    }

    public function addToUrl(string $url): void
    {
        $this->_url .= $url;
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

    public function selfFail(\Exception $e) {
        self::fail("{$e->getMessage()}\n\r{$this->getDebugString()}");
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
            $this->selfFail($e);
        }
    }

    public function checkResponseWithMessage(string $receivedMessage = ''): void
    {
        $this->checkResponse();
        self::assertArrayHasKey('message', $this->_responseJson);
        self::assertSame($receivedMessage, $this->_responseJson['message']);
    }
}