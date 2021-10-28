<?php

namespace App\Tests\Traits;

trait ProductCheckTrait
{
    private function checkProductHaveFields($product): bool
    {
        return is_array($product) &&
               array_key_exists('productId', $product) &&
               array_key_exists('name', $product);
    }

    private function checkProductInExpected($product, array $expectedProductNames = []): bool
    {
        if (empty($expectedProductNames)) {
            return false;
        }

        return $this->checkProductHaveFields($product) &&
               in_array($product['name'], $expectedProductNames, true);
    }
}