<?php

namespace App\Controller;

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

    public function __construct(OrderService $orderService) {
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
     * @Route("/delete", name="order_delete", methods={"POST"})
     */
    public function delete(Request $request): Response
    {
        try {
            $this->orderService->delete($request);
        } catch (HttpException $e) {
            return $this->json(['message'=>$e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            return $this->json(['message'=>$e->getMessage()], RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
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
            $this->orderService->addProduct($request);
        } catch (HttpException $e) {
            return $this->json(['message'=>$e->getMessage()], $e->getStatusCode());
        }
        return $this->json(['message'=>'product added to the order']);
    }

    /**
     * @Route("/remove_product", name="order_remove_product", methods={"POST"})
     */
    public function removeProduct(Request $request): Response
    {
        try {
            $this->orderService->removeProduct($request);
        } catch (HttpException $e) {
            return $this->json(['message'=>$e->getMessage()], $e->getStatusCode());
        }
        return $this->json(['message'=>'product removed from the order']);
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
//            'id' => $order->getId()
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
