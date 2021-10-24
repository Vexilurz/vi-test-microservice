<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;

class OrderProductRemoveService extends OrderProductService
{
    protected function orderProductAction(Order $order, Product $product): string
    {
        $order->removeProduct($product);
        return 'product removed from order';
    }
}