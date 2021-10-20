<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class AuthController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // name="app_login" must match with RequestChecker LOGIN_ROUTE constant
    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request): Response
    {
        if (!$request->isMethod('POST')) {
            return $this->json(['message'=>'Must be a POST method'], Response::HTTP_BAD_REQUEST);
        }

        $email = $request->request->get('email', '');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $apiToken = md5(microtime());
        $user->setApiToken($apiToken);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'login success',
            'apiToken' => $apiToken
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(Request $request): Response
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['apiToken' => $apiToken]);
        if (null === $user) {
            throw new UserNotFoundException();
        }
        $user->setApiToken(null);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'logout success'
        ]);
    }
}
