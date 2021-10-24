<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    public function __construct(OrderRepository $orderRepository,
                                UserRepository $userRepository) {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Route("/get_orders", name="user_get_orders", methods={"GET"})
     */
    public function getOrders(Request $request): Response
    {
        $user = $this->userRepository->getFromRequest($request);
        $orders = $user->getOrdersSerialized();

        return $this->json($orders);
    }

    /**
     * @Route("/get_orders/{userId}", name="user_get_orders_by_userid", methods={"GET"})
     */
    public function getOrdersByUserId($userId): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
            'id' => $userId
        ]);
    }
}
