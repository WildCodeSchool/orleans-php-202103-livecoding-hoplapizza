<?php

namespace App\Controller;

use App\Model\DishManager;

class OrderController extends AbstractController
{
    /**
     * Add dish to an order
     */
    public function add(int $id)
    {
        $dishManager = new DishManager();
        $dish = $dishManager->selectOneById($id);
        if ($dish) {
            $_SESSION['order'][$id] = $dish;
        }
        header("Location: /order/index");
    }

    /**
     * List an order
     */
    public function index()
    {
        $dishes = $_SESSION['order'] ?? [];
        return $this->twig->render('Order/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }
}
