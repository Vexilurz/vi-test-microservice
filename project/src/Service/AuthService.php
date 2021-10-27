<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\TokenGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthService
{
    private UserRepository $userRepository;
    private TokenGenerator $tokenGenerator;

    public function __construct(UserRepository $userRepository,
                                TokenGenerator $tokenGenerator)
    {
        $this->userRepository = $userRepository;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function login(Request $request): User
    {
        $email = $request->request->get('email', '');
        $newApiToken = $this->tokenGenerator->getNewApiToken();

        return $this->userRepository->login($email, $newApiToken);
    }

    public function register(Request $request): User
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        if (!$email || !$password) {
            throw new BadRequestHttpException('email or password is empty');
        }
        //TODO: add validators
        try {
            $this->userRepository->findByEmail($email);
        } catch (HttpException $e) {
            return $this->userRepository->create($email, $password);
        }
        throw new BadRequestHttpException('user already exists');
    }

    public function logout(Request $request): User
    {
        $apiToken = $this->getApiTokenFromRequest($request);
        $user = $this->userRepository->findByApiToken($apiToken);

        return $this->userRepository->logout($user->getEmail());
    }

    public function getApiTokenFromRequest(Request $request): string
    {
        return $request->headers->get('X-AUTH-TOKEN');
    }
}