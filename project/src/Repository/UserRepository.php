<?php

namespace App\Repository;

use App\Entity\User;
use App\Utils\TokenGenerator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private $encoder;
    private TokenGenerator $tokenGenerator;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $encoder,
                                TokenGenerator $tokenGenerator)
    {
        parent::__construct($registry, User::class);
        $this->encoder = $encoder;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function create(string $email, string $password): User {
        $newUser = new User();
        $newUser->setEmail($email);
        $newUser->setPassword($this->encoder->hashPassword($newUser, $password));
        $newUser->setApiToken($this->tokenGenerator->getNewApiToken());
        $newUser->setRoles(['ROLE_USER']);
        $this->_em->persist($newUser);
        $this->_em->flush();

        return $newUser;
    }

    public function login(Request $request): User {
        $email = $request->request->get('email', '');
        $user = $this->findOneBy(['email' => $email]);
        $user->setApiToken($this->tokenGenerator->getNewApiToken());
        $this->_em->flush();

        return $user;
    }

    public function logout(User $user): User {
        $user->setApiToken(null);
        $this->_em->flush();
        return $user;
    }

    public function deleteByEmail(string $email) {
        $user = $this->findOneBy(['email' => $email]);
        $this->_em->remove($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
