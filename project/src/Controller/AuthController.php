<?php

namespace App\Controller;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * This route method is not guarding by authenticators
     *
     * name="app_login" must match with
     * Security\RequestChecker\RegistrationRequestChecker::REGISTRATION_ROUTE constant
     *
     * @Route("/register", name="app_registration", methods={"POST"})
     */
    public function register(Request $request): Response
    {
        try {
            $user = $this->authService->register($request);
        } catch (HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        return $this->json([
            'message' => 'registration success'
        ]);
    }

    /**
     * This route method guards by Security\LoginAuthenticator
     *
     * name="app_login" must match with
     * Security\RequestChecker\LoginRequestChecker::LOGIN_ROUTE constant
     *
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        try {
            $user = $this->authService->login($request);
        } catch (HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        return $this->json([
            'message'  => 'login success',
            'apiToken' => $user->getApiToken()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(Request $request): Response
    {
        try {
            $user = $this->authService->logout($request);
        } catch (HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        return $this->json([
            'message' => 'logout success'
        ]);
    }
}
