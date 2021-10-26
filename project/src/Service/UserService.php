<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Utils\JsonConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;
    private AuthService $authService;

    public function __construct(OrderRepository $orderRepository,
                                UserRepository $userRepository,
                                AuthService $authService) {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->authService = $authService;
    }

    public function getFromRequest(Request $request): User
    {
        $apiToken = $this->authService->getApiTokenFromRequest($request);
        $user = $this->userRepository->findByApiToken($apiToken);
        return $user;
    }

    public function getSerializedOrders(Request $request, $userId = null): array
    {
        if ($userId) {
            $user = $this->userRepository->find($userId);
            if (!$user) { throw new NotFoundHttpException('user not found'); }
        } else {
            $user = $this->getFromRequest($request);
        }

        $onlyPaid = $request->query->get('paid');
        $orders = $onlyPaid ?
            $this->orderRepository->findPaidUserOrders($user) :
            $user->getOrders()->getValues();

        return JsonConverter::getJsonFromEntitiesArray($orders);
    }
}