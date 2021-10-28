<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProduct[]    findAll()
 * @method OrderProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    /**
     * @param Order $order
     * @param Product $product
     * @return OrderProduct|null
     * @throws NonUniqueResultException
     */
    public function findOrderProduct(Order $order, Product $product)
    {
        return $this->createQueryBuilder('op')
            ->andWhere('op.order = :order')
            ->andWhere('op.product = :product')
            ->setParameters(['order' => $order, 'product' => $product])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
