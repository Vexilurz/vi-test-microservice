<?php

namespace App\Controller;

use App\Service\OrderService;
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
    private OrderService $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/create", name="order_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $order = $this->service->create($request);

        return $this->json([
            'message' => 'new order created',
            'orderId' => $order->getId()
        ]);
    }

    /**
     * @Route("/delete", name="order_delete", methods={"POST"})
     */
    public function delete(Request $request): Response
    {
        try {
            $this->service->delete($request);
        } catch (HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        return $this->json([
            'message' => 'order deleted'
        ]);
    }

    /**
     * @Route("/add_product", name="order_add_product", methods={"POST"})
     */
    public function addProduct(Request $request): Response
    {
        try {
            $orderProduct = $this->service->addProduct($request);
        } catch (HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        return $this->json([
            'message' => 'product added to the order',
            'current_product_count' => $orderProduct->getProductCount()
        ]);
    }

    /**
     * @Route("/remove_product", name="order_remove_product", methods={"POST"})
     */
    public function removeProduct(Request $request): Response
    {
        try {
            $this->service->removeProduct($request);
        } catch (HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        return $this->json(['message' => 'product removed from the order']);
    }

    /**
     * @Route("/pay", name="order_pay", methods={"POST"})
     */
    public function pay(Request $request): Response
    {
        try {
            $order = $this->service->getFromRequest($request);
            $this->service->payOrder($order);
        } catch (HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        return $this->json([
            'message' => 'order has been paid',
//            'id' => $order->getId()
        ]);
    }

    /**
     * @Route("/get", name="orders_get", methods={"GET"})
     */
    public function getOrders(Request $request): Response
    {
        $ordersSerialized = $this->service->getSerializedOrders($request);

        return $this->json($ordersSerialized);
    }
}
