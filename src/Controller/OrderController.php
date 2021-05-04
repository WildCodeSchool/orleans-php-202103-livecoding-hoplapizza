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
        $this->increment($id);
    }

    /**
     * Sub dish to an order
     */
    public function substract(int $id)
    {
        $this->increment($id, -1);
    }

    private function increment(int $id, int $increment = 1)
    {
        $dishManager = new DishManager();
        $dish = $dishManager->selectOneById($id);

        if ($dish) {
            $dish['quantity'] = ($_SESSION['order'][$id]['quantity'] ?? 0) + $increment;
            $_SESSION['order'][$id] = $dish;
            if ($_SESSION['order'][$id]['quantity'] <= 0) {
                unset($_SESSION['order']);
            }
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

    /**
     * Empty cart
     */
    public function empty()
    {
        unset($_SESSION['order']);
        header("Location: /order/index");
    }
}
