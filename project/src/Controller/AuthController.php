<?php

namespace App\Controller;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // name="app_login" must match with LoginRequestChecker LOGIN_ROUTE constant
    // guards by LoginAuthenticator
    /**
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        $user = $this->authService->login($request);

        return $this->json([
            'message' => 'login success',
            'apiToken' => $user->getApiToken()
        ]);
    }

    // name="app_login" must match with RegistrationRequestChecker REGISTRATION_ROUTE constant
    // this method is not guarding by authenticators
    /**
     * @Route("/register", name="app_registration", methods={"POST"})
     */
    public function register(Request $request): Response
    {
        try {
            $user = $this->authService->register($request);
        } catch (BadRequestException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'message' => 'registration success'
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(Request $request): Response
    {
        $user = $this->authService->logout($request);

        return $this->json([
            'message' => 'logout success'
        ]);
    }
}
