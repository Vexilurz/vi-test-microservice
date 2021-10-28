<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $em;

    public function __construct(UserPasswordHasherInterface $encoder, EntityManagerInterface $entityManager)
    {
        $this->encoder = $encoder;
        $this->em = $entityManager;
    }

    public function load(ObjectManager $manager)
    {
        $this->prepareUsers();
        $this->prepareProducts();
        $this->em->flush();
        $this->prepareOrders();
        $this->em->flush();
    }

    private function prepareUsers(): void
    {
        $usersData = [
            [
                'email'    => 'test@example.com',
                'password' => 111111,
                'apiToken' => 'test_token',
            ],
            [
                'email'    => 'test2@example.com',
                'password' => 111111,
                'apiToken' => 'test_token2',
            ],
            [
                'email'    => 'user@example.com',
                'password' => 123456,
                'apiToken' => null,
            ],
            [
                'email'    => 'user2@example.com',
                'password' => 111111,
                'apiToken' => null,
            ],
        ];
        foreach ($usersData as $user) {
            $newUser = new User();
            $newUser
                ->setEmail($user['email'])
                ->setPassword($this->encoder->hashPassword($newUser, $user['password']))
                ->setRoles(['ROLE_USER'])
                ->setApiToken($user['apiToken']);
            $this->em->persist($newUser);
        }
    }

    private function prepareProducts()
    {
        $productsData = [
            [
                'name'      => 'Microphone',
                'price'     => 12400,
                'available' => 5,
            ],
            [
                'name'      => 'Guitar',
                'price'     => 35125,
                'available' => 1,
            ],
            [
                'name'      => 'Keyboard',
                'price'     => 8145,
                'available' => 5,
            ],
        ];
        foreach ($productsData as $product) {
            $dateTime = new DateTimeImmutable('now');
            $newProduct = new Product();
            $newProduct
                ->setName($product['name'])
                ->setPrice($product['price'])
                ->setAvailable($product['available'])
                ->setCreatedAt($dateTime)
                ->setUpdatedAt($dateTime);
            $this->em->persist($newProduct);
        }
    }

    private function prepareOrders()
    {
        $productsRepository = $this->em->getRepository(Product::class);
        $products[] = $productsRepository->findOneBy(['name' => 'Microphone']);
        $products[] = $productsRepository->findOneBy(['name' => 'Guitar']);
        $products[] = $productsRepository->findOneBy(['name' => 'Keyboard']);

        $user = $this->em->getRepository(User::class)->findByEmail('test@example.com');
        $user2 = $this->em->getRepository(User::class)->findByEmail('test2@example.com');

        $dateTime = new DateTimeImmutable('2021-10-05');
        $newOrder = new Order();
        $newOrder
            ->setUser($user)
            ->setPaid(true)
            ->setTotalPrice($products[0]->getPrice() + $products[1]->getPrice())
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime);
        $this->em->persist($newOrder);
        $this->addProduct($newOrder, $products[0], 5);
        $this->addProduct($newOrder, $products[1], 2);

        $dateTime = new DateTimeImmutable('2021-10-10');
        $newOrder = new Order();
        $newOrder
            ->setUser($user)
            ->setPaid(false)
            ->setTotalPrice(0)
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime);
        $this->em->persist($newOrder);

        $dateTime = new DateTimeImmutable('2021-10-15');
        $newOrder = new Order();
        $newOrder
            ->setUser($user)
            ->setPaid(false)
            ->setTotalPrice($products[1]->getPrice() + $products[2]->getPrice())
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime);
        $this->em->persist($newOrder);
        $this->addProduct($newOrder, $products[1], 1);
        $this->addProduct($newOrder, $products[2], 4);

        $dateTime = new DateTimeImmutable('2021-10-20');
        $newOrder = new Order();
        $newOrder
            ->setUser($user2)
            ->setPaid(true)
            ->setTotalPrice($products[0]->getPrice() + $products[2]->getPrice())
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime);
        $this->em->persist($newOrder);
        $this->addProduct($newOrder, $products[0], 3);
        $this->addProduct($newOrder, $products[2], 1);
    }

    private function addProduct(Order $order, Product $product, int $count = 1)
    {
        $orderProduct = new OrderProduct();
        $orderProduct
            ->setOrder($order)
            ->setProduct($product)
            ->setProductCount($count);
        $this->em->persist($orderProduct);
    }
}
