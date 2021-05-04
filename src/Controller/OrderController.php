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
        header("Location: order/index");
    }
}
