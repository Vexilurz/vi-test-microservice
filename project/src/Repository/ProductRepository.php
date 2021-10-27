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
        $newProduct = new Product();
        $newProduct->setName($name);
        $newProduct->setPrice($price);
        $datetime = new DateTimeImmutable('now');
        $newProduct->setCreatedAt($datetime);
        $newProduct->setUpdatedAt($datetime);
        $newProduct->setAvailable(true);
        $this->_em->persist($newProduct);
        $this->_em->flush();

        return $newProduct;
    }

    //TODO: think about Product arg instead of id
//    public function delete(int $id) {
//        $product = $this->find($id);
//        $this->_em->remove($product);
//        $this->_em->flush();
//    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findAvailableInOrder(Order $order): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.orders', 'o', 'WITH', 'o = :order')
            ->andWhere('p.available = :available')
            ->setParameters(['order' => $order, 'available' => true])
            ->getQuery()
            ->getResult();
    }
}
