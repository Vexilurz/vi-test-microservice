<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    private OrderProductRepository $orderProductRepository;

    public function __construct(ManagerRegistry $registry, OrderProductRepository $orderProductRepository)
    {
        parent::__construct($registry, Order::class);
        $this->orderProductRepository = $orderProductRepository;
    }

    public function create(User $user): Order
    {
        $datetime = new DateTimeImmutable('now');
        $newOrder = new Order();
        $newOrder
            ->setUser($user)
            ->setTotalPrice(0)
            ->setPaid(false)
            ->setCreatedAt($datetime)
            ->setUpdatedAt($datetime);
        $this->_em->persist($newOrder);
        $this->_em->flush();

        return $newOrder;
    }

    public function delete(Order $order)
    {
        $this->_em->remove($order);
        $this->_em->flush();
    }

    public function setPaid(Order $order, bool $paid): Order
    {
        $order
            ->setPaid($paid)
            ->setUpdatedAt(new DateTimeImmutable('now'));
        $this->_em->flush();

        return $order;
    }

    public function addProduct(Order $order, Product $product, int $productCount = 1): OrderProduct
    {
        if ($productCount < 1) {
            throw new \Exception('product count must be greater than zero');
        }

        $orderProduct = $this->orderProductRepository->findOrderProduct($order, $product);
        // if product already exist in order:
        if ($orderProduct) {
            $orderProduct->addProductCount($productCount);
        } else {
            $orderProduct = new OrderProduct();
            $orderProduct
                ->setOrder($order)
                ->setProduct($product)
                ->setProductCount($productCount);
            $this->_em->persist($orderProduct);
        }
        $order
            ->setTotalPrice($order->getTotalPrice() + $product->getPrice() * $productCount)
            ->setUpdatedAt(new \DateTimeImmutable('now'));
        $this->_em->flush();

        return $orderProduct;
    }

    public function removeProduct(Order $order, Product $product): void
    {
        $orderProduct = $this->orderProductRepository->findOrderProduct($order, $product);
        if (!$orderProduct) {
            throw new \Exception('product not exist in order');
        }

        $order
            ->setTotalPrice($order->getTotalPrice() - $product->getPrice() * $orderProduct->getProductCount())
            ->setUpdatedAt(new \DateTimeImmutable('now'));
        $this->_em->remove($orderProduct);
        $this->_em->flush();
    }

    /**
     * @return Order[] Returns an array of Order objects
     */
    public function findPaidUserOrders(User $user): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->andWhere('o.paid = :paid')
            ->setParameters(['user' => $user, 'paid' => true])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Order[] Returns an array of Order objects
     */
    public function findOrdersByDate(DateTimeImmutable $fromDate, DateTimeImmutable $toDate): array
    {
        $qb = $this->createQueryBuilder('o');

        return $qb
            ->andWhere($qb->expr()->between('o.createdAt', ':fromDate', ':toDate'))
            ->setParameters(['fromDate' => $fromDate, 'toDate' => $toDate])
            ->getQuery()
            ->getResult();
    }
}
