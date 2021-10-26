<?php

namespace App\Tests\Order;

use App\Tests\VitmWithIdsWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderGetTest extends VitmWithIdsWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setUrl('/order/get');
    }

    public function testGetAllOrders(): void
    {
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 3);
        self::assertSame($this->getResponseJson()[0]['id'], $this->getFirstOrderId());
        //TODO: create something common with UserGetOrdersTest to test received orders
    }

    public function testGetOrdersWithCoveredTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-01&toDate=2021-10-20');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 3);
        self::assertSame($this->getResponseJson()[0]['id'], $this->getFirstOrderId());
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
        self::assertSame($this->getResponseJson()[0]['id'], $this->getFirstOrderId());
    }

    public function testGetSecondOrderByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-07&toDate=2021-10-13');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 1);
        self::assertSame($this->getResponseJson()[0]['id'], $this->getFirstOrderId() + 1);
    }

    public function testGetThirdOrderByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-13');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 1);
        self::assertSame($this->getResponseJson()[0]['id'], $this->getFirstOrderId() + 2);
    }

    public function testGetLastTwoOrdersByTime(): void
    {
        $this->addToUrl('?fromDate=2021-10-07');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 2);
        self::assertSame($this->getResponseJson()[0]['id'], $this->getFirstOrderId() + 1);
    }

    public function testGetFirstTwoOrdersByTime(): void
    {
        $this->addToUrl('?toDate=2021-10-13');
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 2);
        self::assertSame($this->getResponseJson()[0]['id'], $this->getFirstOrderId());
    }
}
