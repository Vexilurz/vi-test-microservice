<?php

namespace App\Tests\Order;

use App\Tests\Traits\OrderCheckTrait;
use App\Tests\VitmBaseWebTestCase;

class OrderGetTest extends VitmBaseWebTestCase
{
    use OrderCheckTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUrl('/order/get');
    }

    private function checkOrdersCountAndFirstId(int $expectedCount, int $expectedFirstOrderId = -1) {
        $orders = $this->getResponseJson();
        self::assertSame(count($orders), $expectedCount);
        if ($expectedCount > 0) {
            foreach ($orders as $order) {
                self::assertTrue($this->checkOrderHaveFields($order));
            }
            self::assertSame($orders[0]['orderId'], $expectedFirstOrderId);
        }
    }

    public function testGetAllOrders(): void
    {
        $this->checkResponse();
        $this->checkOrdersCountAndFirstId(4, 1);
    }

    public function testGetOrdersWithCoveredTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-01&toDate=2021-10-25');
        $this->checkResponse();
        $this->checkOrdersCountAndFirstId(4, 1);
    }

    public function testGetOrdersWithNoMatchingTime(): void
    {
        $this->addToUrl('?toDate=2021-10-01');
        $this->checkResponse();
        $this->checkOrdersCountAndFirstId(0);
    }

    public function testGetFirstOrderByTime(): void
    {
        $this->addToUrl('?toDate=2021-10-07');
        $this->checkResponse();
        $this->checkOrdersCountAndFirstId(1, 1);
    }

    public function testGetSecondOrderByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-07&toDate=2021-10-13');
        $this->checkResponse();
        $this->checkOrdersCountAndFirstId(1, 2);
    }

    public function testGetThirdOrderByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-13&toDate=2021-10-17');
        $this->checkResponse();
        $this->checkOrdersCountAndFirstId(1, 3);
    }

    public function testGetSecondAndThirdOrderByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-07&toDate=2021-10-17');
        $this->checkResponse();
        $this->checkOrdersCountAndFirstId(2, 2);
    }

    public function testGetFourthOrderByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-17');
        $this->checkResponse();
        $this->checkOrdersCountAndFirstId(1, 4);
    }

    public function testGetLastTwoOrdersByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-13');
        $this->checkResponse();
        $this->checkOrdersCountAndFirstId(2, 3);
    }

    public function testGetFirstTwoOrdersByTime(): void
    {
        $this->addToUrl('?toDate=2021-10-13');
        $this->checkResponse();
        $this->checkOrdersCountAndFirstId(2, 1);
    }
}
