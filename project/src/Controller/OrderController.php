<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Service\OrderProductAddService;
use App\Service\OrderProductRemoveService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order", name="order")
 */
class OrderController extends AbstractController
{
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    public function __construct(OrderRepository $orderRepository,
                                UserRepository $userRepository) {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Route("/create", name="order_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $user = $this->userRepository->getUserFromRequest($request);
        $order = $this->orderRepository->create($user);

        return $this->json([
            'message' => 'new order created',
            'id' => $order->getId()
        ]);
    }

    /**
     * @Route("/add_product", name="order_add_product", methods={"POST"})
     */
    public function addProduct(Request $request, OrderProductAddService $orderService): Response
    {
        return $orderService->updateProduct($request);
    }

    /**
     * @Route("/remove_product", name="order_remove_product", methods={"POST"})
     */
    public function removeProduct(Request $request, OrderProductRemoveService $orderService): Response
    {
        return $orderService->updateProduct($request);
    }

    /**
     * @Route("/pay", name="order_pay", methods={"POST"})
     */
    public function pay(Request $request, EntityManagerInterface $entityManager): Response
    {
        $orderId = $request->request->get('orderId', 0);
        $order = $this->orderRepository->find($orderId);
        $userFromRequest = $this->userRepository->getUserFromRequest($request);

        if (!$order) {
            return new JsonResponse(['message'=>'order not found'],
                Response::HTTP_BAD_REQUEST);
        }

        if ($order->getUser() !== $userFromRequest) {
            return new JsonResponse(
                ['message'=>'user from apiToken are not the owner of that order'],
                Response::HTTP_FORBIDDEN
            );
        }

        $order->setPaid(true);
        $entityManager->flush();

        return $this->json([
            'message' => 'order has been paid',
            'id' => $order->getId()
        ]);
    }
}
