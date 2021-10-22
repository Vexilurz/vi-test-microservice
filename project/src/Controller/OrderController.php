<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order", name="order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/create", name="order_create")
     */
    public function create(Request $request, OrderRepository $orderRepository, UserRepository $userRepository): Response
    {
        if (!$request->isMethod('POST')) {
            return $this->json(['message'=>'Must be a POST method'], Response::HTTP_BAD_REQUEST);
        }

        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        $user = $userRepository->findOneBy(['apiToken' => $apiToken]);
        $order = $orderRepository->create($user);

        return $this->json([
            'message' => 'new order created',
            'id' => $order->getId()
        ]);
    }
}
