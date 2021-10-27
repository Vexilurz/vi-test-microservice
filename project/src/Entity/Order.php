<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use App\Utils\JsonConverter;
use App\Utils\JsonConverterInterface;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order implements JsonConverterInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private $paid;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(
     *     targetEntity=OrderProduct::class,
     *     mappedBy="order",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"})
     */
    private $products;

    /**
     * @ORM\Column(type="float", options={"default" : 0})
     */
    private $totalPrice;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getJsonArray(array $options = []): array
    {
        $products = $this->getProducts();
        $productsSerialized = JsonConverter::getJsonFromEntitiesArray($products);
        $result = [
            'orderId' => $this->getId(),
            'paid' => $this->getPaid(),
            'totalPrice' => $this->getTotalPrice(),
            'products' => $productsSerialized,
            'createdAt' => $this->getCreatedAt()->getTimestamp(),
            'updatedAt' => $this->getUpdatedAt()->getTimestamp()
        ];
        if (array_key_exists('includeUser', $options) && $options['includeUser']) {
            $result['user'] = $this->getUser()->getJsonArray();
        }

        return $result;
    }

    /**
     * @return Collection|OrderProduct[]
     */
    private function getOrderProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        $result = [];
        foreach ($this->getOrderProducts()->getValues() as $entity) {
            $result[] = $entity->product;
        }

        return $result;
    }

    public function addProduct(OrderProduct $orderProduct): self
    {
        if ($this->products->contains($orderProduct)) {
            return $this;
        }
        $this->products[] = $orderProduct;
        // needed to update the owning side of the relationship!
        $orderProduct->setOrder($this);

        return $this;
    }

    public function removeProduct(OrderProduct $orderProduct): self
    {
        if (!$this->products->contains($orderProduct)) {
            return $this;
        }
        $this->products->removeElement($orderProduct);
        // needed to update the owning side of the relationship!
        $orderProduct->setOrder(null);

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
