<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(string $name, float $price): Product
    {
        $datetime = new DateTimeImmutable('now');
        $newProduct = new Product();
        $newProduct
            ->setName($name)
            ->setPrice($price)
            ->setCreatedAt($datetime)
            ->setUpdatedAt($datetime)
            ->setAvailable(true);
        $this->_em->persist($newProduct);
        $this->_em->flush();

        return $newProduct;
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findAvailableInOrder(Order $order): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.orders', 'o', 'WITH', 'o.order = :order')
            ->andWhere('p.available > :count')
            ->setParameters(['order' => $order->getId(), 'count' => 0])
            ->getQuery()->getResult();
    }
}
