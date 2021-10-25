<?php

namespace App\Tests\User;

use App\Tests\VitmWithIdsWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserGetOrdersTest extends VitmWithIdsWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setUrl('/user/get_orders');
    }

    private function checkExpectedProducts($order, array $expectedProducts = []) {
        if (count($expectedProducts) > 0) {
            foreach ($order['products'] as $product) {
                self::assertContains($product['name'], $expectedProducts);
            }
        } else {
            self::assertSame(count($order['products']), 0);
        }
    }

    private function checkReceivedOrders() {
        self::assertSame(count($this->getResponseJson()), 3);
        $this->checkExpectedProducts($this->getResponseJson()[0], ['Microphone', 'Guitar']);
        $this->checkExpectedProducts($this->getResponseJson()[1]);
        $this->checkExpectedProducts($this->getResponseJson()[2], ['Keyboard', 'Guitar']);
    }

    public function testGetUserOrdersFromApiToken(): void
    {
        $this->checkResponse();
        $this->checkReceivedOrders();
    }

    public function testGetUserOrdersFromUserId(): void
    {
        $this->addToUrl("/{$this->getTestUserId()}");
        $this->checkResponse();
        $this->checkReceivedOrders();
    }

    public function testGetUserOrdersFromInvalidUserId(): void
    {
        $this->addToUrl("/-1");
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('user not found');
    }
}
