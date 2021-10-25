<?php

namespace App\Tests\Order;

use App\Entity\Order;
use App\Entity\User;
use App\Tests\VitmWithIdsWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteOrderTest extends VitmWithIdsWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/delete');
    }

    public function testDeleteOrder(): void
    {
        try {
            $user = $this->getEntityManager()->getRepository(User::class)
                ->find($this->getTestUserId());
            $order = $this->getEntityManager()->getRepository(Order::class)
                ->create($user);
        } catch(\Exception $e) {
            $this->selfFail($e);
        }

        $this->setBody(['orderId'=>$order->getId()]);
        $this->checkResponseWithMessage('order deleted');
    }

    public function testNotOwnerDeleteOrder(): void
    {
        $this->setApiToken('test_token2');
        $this->setBody(['orderId'=>$this->getFirstOrderId()]);
        $this->setResponseCode(Response::HTTP_FORBIDDEN);
        $this->checkResponseWithMessage('user is not the owner of the order');
    }

    public function testDeleteOrderWithWrongId(): void
    {
        $this->setBody(['orderId'=>-1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }
}
