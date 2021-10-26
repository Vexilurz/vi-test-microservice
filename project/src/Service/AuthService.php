<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

class AuthService
{
    private UserRepository $userRepository;
    private UserService $userService;

    public function __construct(UserRepository $userRepository,
                                UserService $userService) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    public function login(Request $request): User
    {
        $email = $request->request->get('email', '');
        return $this->userRepository->login($email);
    }

    public function register(Request $request): User
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        if (!$email || !$password) {
            throw new BadRequestException('email or password is empty');
        }
        //TODO: add validators
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user) {
            throw new BadRequestException('user already exists');
        }

        return $this->userRepository->create($email, $password);
    }

    public function logout(Request $request): User
    {
        $user = $this->userService->getFromRequest($request);
        return $this->userRepository->logout($user);
    }
}