<?php

namespace App\Tests\User;

use App\Tests\Traits\OrderCheckTrait;
use App\Tests\Traits\ProductCheckTrait;
use App\Tests\VitmBaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserGetOrdersTest extends VitmBaseWebTestCase
{
    use ProductCheckTrait;
    use OrderCheckTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUrl('/user/get_orders');
    }

    private function checkExpectedProductsInOrder($order, array $expectedProducts = []): void {
        self::assertTrue($this->checkOrderHaveFields($order), 'Order do not have expected fields');
        if (count($expectedProducts) > 0) {
            foreach ($order['products'] as $product) {
                self::assertTrue($this->checkProductInExpected($product, $expectedProducts));
            }
        } else {
            self::assertSame(count($order['products']), 0);
        }
    }

    private function checkUser1ReceivedOrders() {
        $orders = $this->getResponseJson();
        self::assertSame(count($orders), 3);
        $this->checkExpectedProductsInOrder($orders[0], ['Microphone', 'Guitar']);
        $this->checkExpectedProductsInOrder($orders[1]);
        $this->checkExpectedProductsInOrder($orders[2], ['Keyboard', 'Guitar']);
    }

    public function testGetUserOrdersFromApiToken(): void
    {
        $this->checkResponse();
        $this->checkUser1ReceivedOrders();
    }

    public function testGetUserPaidOrdersFromApiToken(): void
    {
        $this->addToUrl('?paid=1');
        $this->checkResponse();
        $orders = $this->getResponseJson();
        self::assertSame(count($orders), 1);
        $this->checkExpectedProductsInOrder($orders[0], ['Microphone', 'Guitar']);
    }

    public function testGetUserOrdersFromUserId1(): void
    {
        $this->addToUrl("/1");
        $this->checkResponse();
        $this->checkUser1ReceivedOrders();
    }

    public function testGetUserOrdersFromUserId2(): void
    {
        $this->addToUrl("/2");
        $this->checkResponse();
        $orders = $this->getResponseJson();
        self::assertSame(count($orders), 1);
        $this->checkExpectedProductsInOrder($orders[0], ['Microphone', 'Keyboard']);
    }

    public function testGetUserOrdersFromInvalidUserId(): void
    {
        $this->addToUrl("/-1");
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('user not found');
    }
}
