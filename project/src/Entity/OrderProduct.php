<?php

namespace App\Entity;

use App\Utils\JsonConverterInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_product",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="order_product_unique", columns={"order_id", "product_id"})
 *    })
 */
class OrderProduct implements JsonConverterInterface
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private Order $order;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private Product $product;

    /**
     * @ORM\Column(type="integer", options={"default" : 1})
     */
    private int $productCount;

    public function getJson(array $options = []): array
    {
        $jsonOrder = $this->order->getJson();
        $jsonOrder['products'] = $this->product->getJson();

        return $jsonOrder;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getProductCount(): int
    {
        return $this->productCount;
    }

    public function setProductCount(int $productCount): self
    {
        $this->productCount = $productCount;

        return $this;
    }

    public function addProductCount(int $productCount): self
    {
        $this->productCount += $productCount;

        return $this;
    }
}