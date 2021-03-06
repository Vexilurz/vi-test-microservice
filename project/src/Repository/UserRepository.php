<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use function get_class;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private $encoder;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $encoder)
    {
        parent::__construct($registry, User::class);
        $this->encoder = $encoder;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findByApiToken(string $apiToken): User
    {
        $user = $this->findOneBy(['apiToken' => $apiToken]);
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function findByEmail(string $email): User
    {
        $user = $this->findOneBy(['email' => $email]);
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function create(string $email, string $password): User
    {
        $newUser = new User();
        $newUser
            ->setEmail($email)
            ->setPassword($this->encoder->hashPassword($newUser, $password))
            ->setRoles(['ROLE_USER']);
        $this->_em->persist($newUser);
        $this->_em->flush();

        return $newUser;
    }

    public function setApiToken(User $user, $newApiToken): User
    {
        $user->setApiToken($newApiToken);
        $this->_em->flush();

        return $user;
    }
}
