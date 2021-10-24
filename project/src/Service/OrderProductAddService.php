<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;

class OrderProductAddService extends OrderProductService
{
    protected function orderProductAction(Order $order, Product $product): string
    {
        $order->addProduct($product);
        return 'product added to order';
    }
}