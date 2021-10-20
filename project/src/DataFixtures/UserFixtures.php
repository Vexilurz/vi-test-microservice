<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    private $em;

    public function __construct(UserPasswordHasherInterface $encoder, EntityManagerInterface $entityManager)
    {
        $this->encoder = $encoder;
        $this->em = $entityManager;
    }

    public function load(\Doctrine\Persistence\ObjectManager $manager)
    {
        $usersData = [
            0 => [
                'email' => 'user@example.com',
                'role' => ['ROLE_USER'],
                'password' => 123456,
                'api_token' => '123qwe'
            ],
            1 => [
                'email' => 'user2@example.com',
                'role' => ['ROLE_USER'],
                'password' => 111111,
                'api_token' => '123qwezxc'
            ]
        ];

        foreach ($usersData as $user) {
            $newUser = new User();
            $newUser->setEmail($user['email']);
            $newUser->setPassword($this->encoder->hashPassword($newUser, $user['password']));
            $newUser->setRoles($user['role']);
            $newUser->setApiToken($user['api_token']);
            $this->em->persist($newUser);
        }

        $this->em->flush();
    }
}
