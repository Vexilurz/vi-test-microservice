<?php

namespace App\Tests\Order;

use App\Entity\Order;
use App\Tests\VitmWithIdsWebTestCase;

class OrderCreateTest extends VitmWithIdsWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/create');
    }

    public function testCreateOrder(): void
    {
        $this->checkResponseWithMessage('new order created');
        self::assertArrayHasKey('id', $this->getResponseJson());

        try {
            $id = $this->getResponseJson()['id'];
            $repository = $this->getEntityManager()->getRepository(Order::class);
            $order = $repository->find($id);
            $repository->delete($order);
        } catch(\Exception $e) {
            $this->selfFail($e);
        }
    }
}
