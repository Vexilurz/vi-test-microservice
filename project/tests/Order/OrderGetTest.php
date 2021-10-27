<?php

namespace App\Tests\Order;

use App\Tests\VitmBaseWebTestCase;

class OrderGetTest extends VitmBaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setUrl('/order/get');
    }

    public function testGetAllOrders(): void
    {
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 4);
        self::assertSame($this->getResponseJson()[0]['orderId'], 1);
        //TODO: create something common with UserGetOrdersTest to test received orders
    }

    public function testGetOrdersWithCoveredTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-01&toDate=2021-10-25');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 4);
        self::assertSame($this->getResponseJson()[0]['orderId'], 1);
    }

    public function testGetOrdersWithNoMatchingTime(): void
    {
        $this->addToUrl('?toDate=2021-10-01');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 0);
    }

    public function testGetFirstOrderByTime(): void
    {
        $this->addToUrl('?toDate=2021-10-07');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 1);
        self::assertSame($this->getResponseJson()[0]['orderId'], 1);
    }

    public function testGetSecondOrderByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-07&toDate=2021-10-13');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 1);
        self::assertSame($this->getResponseJson()[0]['orderId'], 2);
    }

    public function testGetThirdOrderByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-13&toDate=2021-10-17');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 1);
        self::assertSame($this->getResponseJson()[0]['orderId'], 3);
    }

    public function testGetFourthOrderByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-17');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 1);
        self::assertSame($this->getResponseJson()[0]['orderId'], 4);
    }

    public function testGetLastTwoOrdersByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-13');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 2);
        self::assertSame($this->getResponseJson()[0]['orderId'], 3);
    }

    public function testGetFirstTwoOrdersByTime(): void
    {
        $this->addToUrl('?toDate=2021-10-13');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 2);
        self::assertSame($this->getResponseJson()[0]['orderId'], 1);
    }
}
