<?php


namespace App\Tests\Traits;


trait OrderCheckTrait
{
    private function checkOrderHaveFields($order): bool
    {
        return is_array($order) &&
            array_key_exists('orderId', $order) &&
            array_key_exists('products', $order) &&
            is_array($order['products']);
    }
}