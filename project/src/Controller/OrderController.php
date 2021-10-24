<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Service\OrderProductAddService;
use App\Service\OrderProductRemoveService;
use App\Service\OrderService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order", name="order")
 */
class OrderController extends AbstractController
{
    private OrderService $orderService;
    private UserService $userService;

    public function __construct(OrderService $orderService,
                                UserService $userService) {
        $this->userService = $userService;
        $this->orderService = $orderService;
    }

    /**
     * @Route("/create", name="order_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $order = $this->orderService->create($request);

        return $this->json([
            'message' => 'new order created',
            'id' => $order->getId()
        ]);
    }

    /**
     * @Route("/add_product", name="order_add_product", methods={"POST"})
     */
    public function addProduct(Request $request, OrderProductAddService $service): Response
    {
        try {
            $message = $service->updateProduct($request);
        } catch (HttpException $e) {
            return $this->json(['message'=>$e->getMessage()], $e->getStatusCode());
        }
        return $this->json(['message'=>$message]);
    }

    /**
     * @Route("/remove_product", name="order_remove_product", methods={"POST"})
     */
    public function removeProduct(Request $request, OrderProductRemoveService $service): Response
    {
        try {
            $message = $service->updateProduct($request);
        } catch (HttpException $e) {
            return $this->json(['message'=>$e->getMessage()], $e->getStatusCode());
        }
        return $this->json(['message'=>$message]);
    }

    /**
     * @Route("/pay", name="order_pay", methods={"POST"})
     */
    public function pay(Request $request): Response
    {
        try {
            $order = $this->orderService->getFromRequest($request);
        }
        catch (HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        $this->orderService->setPaid($order, true);

        return $this->json([
            'message' => 'order has been paid',
            'id' => $order->getId()
        ]);
    }

    /**
     * @Route("/get", name="orders_get", methods={"GET"})
     */
    public function getOrders(Request $request): Response
    {
        try {
            $ordersSerialized = $this->orderService->getSerializedOrders($request);
        } catch (HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], RESPONSE::HTTP_BAD_REQUEST);
        }
        return $this->json($ordersSerialized);
    }
}
