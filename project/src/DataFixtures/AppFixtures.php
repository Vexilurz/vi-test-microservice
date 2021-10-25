<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
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

    private function prepareUsers(): void
    {
        $usersData = [
            [
                'email' => 'user@example.com',
                'password' => 123456,
                'apiToken' => null,
            ],
            [
                'email' => 'user2@example.com',
                'password' => 111111,
                'apiToken' => null,
            ],
            [
                'email' => 'test@example.com',
                'password' => 111111,
                'apiToken' => 'test_token',
            ]
        ];
        foreach ($usersData as $user) {
            $newUser = new User();
            $newUser->setEmail($user['email']);
            $newUser->setPassword($this->encoder->hashPassword($newUser, $user['password']));
            $newUser->setRoles(['ROLE_USER']);
            $newUser->setApiToken($user['apiToken']);
            $this->em->persist($newUser);
        }
    }

    private function prepareProducts() {
        $productsData = [
            [
                'name' => 'Microphone',
                'price' => 12400,
                'available' => true,
            ],
            [
                'name' => 'Guitar',
                'price' => 35125,
                'available' => false,
            ],
            [
                'name' => 'Keyboard',
                'price' => 8145,
                'available' => true,
            ],
        ];
        foreach ($productsData as $product) {
            $newProduct = new Product();
            $newProduct->setName($product['name']);
            $newProduct->setPrice($product['price']);
            $newProduct->setAvailable($product['available']);
            $dateTime = new \DateTimeImmutable('now');
            $newProduct->setCreatedAt($dateTime);
            $newProduct->setUpdatedAt($dateTime);
            $this->em->persist($newProduct);
        }
    }

    private function prepareOrders() {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
        $productsRepository = $this->em->getRepository(Product::class);
        $products[] = $productsRepository->findOneBy(['name' => 'Microphone']);
        $products[] = $productsRepository->findOneBy(['name' => 'Guitar']);
        $products[] = $productsRepository->findOneBy(['name' => 'Keyboard']);

        $newOrder = new Order();
        $newOrder->setUser($user);
        $newOrder->setPaid(true);
        $newOrder->addProduct($products[0]);
        $newOrder->addProduct($products[1]);
        $newOrder->setTotalPrice($products[0]->getPrice() + $products[1]->getPrice());
        $dateTime = new \DateTimeImmutable('now - 15 days');
        $newOrder->setCreatedAt($dateTime);
        $newOrder->setUpdatedAt($dateTime);
        $this->em->persist($newOrder);

        $newOrder = new Order();
        $newOrder->setUser($user);
        $newOrder->setPaid(false);
        $newOrder->setTotalPrice(0);
        $dateTime = new \DateTimeImmutable('now - 10 day');
        $newOrder->setCreatedAt($dateTime);
        $newOrder->setUpdatedAt($dateTime);
        $this->em->persist($newOrder);

        $newOrder = new Order();
        $newOrder->setUser($user);
        $newOrder->setPaid(false);
        $newOrder->addProduct($products[1]);
        $newOrder->addProduct($products[2]);
        $newOrder->setTotalPrice($products[1]->getPrice() + $products[2]->getPrice());
        $dateTime = new \DateTimeImmutable('now - 5 days');
        $newOrder->setCreatedAt($dateTime);
        $newOrder->setUpdatedAt($dateTime);
        $this->em->persist($newOrder);
    }

    public function load(\Doctrine\Persistence\ObjectManager $manager)
    {
        $this->prepareUsers();
        $this->prepareProducts();
        $this->em->flush();
        $this->prepareOrders();
        $this->em->flush();
    }
}
