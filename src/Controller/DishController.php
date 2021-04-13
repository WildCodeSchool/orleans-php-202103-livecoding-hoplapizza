<?php

namespace App\Controller;

use App\Model\DishManager;

class DishController extends AbstractController
{
    /**
     * Display list of dishes
     */
    public function index()
    {
        $dishManager = new DishManager();
        $dishes = $dishManager->selectAll();

        return $this->twig->render('Dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }
}
