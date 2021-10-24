<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function getFromRequest(Request $request): Order {
        $orderId = $request->request->get('orderId', 0);
        $order = $this->find($orderId);
        if (!$order) {
            throw new NotFoundHttpException('order not found');
        }
        return $order;
    }

    public function create(User $user): Order {
        $newOrder = new Order();
        $newOrder->setUser($user);
        $newOrder->setTotalPrice(0);
        $newOrder->setPaid(false);
        $datetime = new \DateTimeImmutable('now');
        $newOrder->setCreatedAt($datetime);
        $newOrder->setUpdatedAt($datetime);
        $this->_em->persist($newOrder);
        $this->_em->flush();
        return $newOrder;
    }

    public function checkOrderBelongsToUser(Order $order, User $user): bool {
        if ($order->getUser() !== $user) {
            throw new AccessDeniedHttpException('user is not the owner of the order');
        }
        return true;
    }

    public function setPaid(Order $order, bool $paid): Order {
        $order->setPaid($paid);
        $this->_em->flush();
        return $order;
    }

    /**
     * @return Order[] Returns an array of Order objects
     */
    public function findPaidUserOrders(User $user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->andWhere('o.paid = :paid')
            ->setParameter('paid', true)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
