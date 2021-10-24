<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;

class OrderProductAddService extends OrderProductService
{
    protected function orderProductAction(Order $order, Product $product): string
    {
        $this->orderRepository->addProduct($order, $product);
        return 'product added to order';
    }
}