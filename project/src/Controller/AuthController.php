<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class AuthController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    // name="app_login" must match with LoginRequestChecker LOGIN_ROUTE constant
    // guards by LoginAuthenticator
    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request): Response
    {
        if (!$request->isMethod('POST')) {
            return $this->json(['message'=>'Must be a POST method'], Response::HTTP_BAD_REQUEST);
        }

        $email = $request->request->get('email', '');
        $user = $this->userRepository->login($email);

        return $this->json([
            'message' => 'login success',
            'apiToken' => $user->getApiToken()
        ]);
    }

    // name="app_login" must match with RegistrationRequestChecker REGISTRATION_ROUTE constant
    // this method is not guarding by authenticators
    /**
     * @Route("/register", name="app_registration")
     */
    public function register(Request $request): Response
    {
        if (!$request->isMethod('POST')) {
            return $this->json(['message'=>'Must be a POST method'], Response::HTTP_BAD_REQUEST);
        }

        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        if (!$email || !$password) {
            return $this->json(['message'=>'email or password is empty'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user) {
            return $this->json(['message'=>'user already exists'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->create($email, $password);

        return $this->json([
            'message' => 'registration success',
            'apiToken' => $user->getApiToken()
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
