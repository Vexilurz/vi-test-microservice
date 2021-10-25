<?php


namespace App\Tests;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;

abstract class VitmWithIdsBaseWebTestCase extends VitmBaseWebTestCase
{
    private int $_firstOrderId = -1;
    private int $_firstProductId = -1;
    private int $_testUserId = -1;

    public function setUp(): void
    {
        parent::setUp();
        $this->_firstOrderId = $this->findFirstId(Order::class);
        $this->_firstProductId = $this->findFirstId(Product::class);
        $this->_testUserId = $this->findFirstId(User::class);
    }

    public function getFirstOrderId(): int {
        return $this->_firstOrderId;
    }

    public function getFirstProductId(): int {
        return $this->_firstProductId;
    }

    public function getTestUserId(): int {
        return $this->_testUserId;
    }

    private function findFirstId(string $entityClassName): int {
        $id = -1;
        try {
            $entities = $this->getEntityManager()
                ->getRepository($entityClassName)
                ->findAll();
            $id = $entities[0]->getId();
        } catch(\Exception $e) {
            $this->selfFail($e);
        }
        return $id;
    }
}