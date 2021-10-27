<?php

namespace App\Repository;

use App\Entity\Order;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function create(User $user): Order
    {
        $newOrder = new Order();
        $newOrder->setUser($user);
        $newOrder->setTotalPrice(0);
        $newOrder->setPaid(false);
        $datetime = new DateTimeImmutable('now');
        $newOrder->setCreatedAt($datetime);
        $newOrder->setUpdatedAt($datetime);
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
        $order->setPaid($paid);
        $order->setUpdatedAt(new DateTimeImmutable('now'));
        $this->_em->flush();

        return $order;
    }

    public function addProduct(Order $order, Product $product): Order
    {
        $order->addProduct($product);
        $order->setTotalPrice($order->getTotalPrice() + $product->getPrice());
        $this->_em->flush();

        return $order;
    }

    public function removeProduct(Order $order, Product $product): Order
    {
        $order->removeProduct($product);
        //TODO: ask about totalPrice: what to do when price of the product changed?
        $order->setTotalPrice($order->getTotalPrice() - $product->getPrice());
        $this->_em->flush();

        return $order;
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
