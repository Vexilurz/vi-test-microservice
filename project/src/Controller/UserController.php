<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
    private UserService $service;

    public function __construct(UserService $service) {
        $this->service = $service;
    }

    /**
     * @Route("/get_orders", name="user_get_orders", methods={"GET"})
     */
    public function getOrders(Request $request): Response
    {
        $ordersSerialized = $this->service->getSerializedOrders($request);
        return $this->json($ordersSerialized);
    }

    /**
     * @Route("/get_orders/{userId}", name="user_get_orders_by_userid", methods={"GET"})
     */
    public function getOrdersByUserId(Request $request, $userId): Response
    {
        try {
            $ordersSerialized = $this->service->getSerializedOrders($request, $userId);
        } catch (HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
        return $this->json($ordersSerialized);
    }
}
