<?php

namespace App\Controller;

use App\Model\DishManager;

class AdminDishController extends AbstractController
{
    public function index(): string
    {
        $dishManager = new DishManager();
        $dishes = $dishManager->selectAll();

        return $this->twig->render('Admin/Dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }
}
