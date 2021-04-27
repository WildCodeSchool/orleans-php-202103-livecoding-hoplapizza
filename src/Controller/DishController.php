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
        $categoryDishes = $dishManager->selectAllWithCategory();
        $categories = [];
        foreach ($categoryDishes as $categoryDish) {
            $category = $categoryDish['category_name'];
            $categories[$category][] = $categoryDish;
        }

        return $this->twig->render('Dish/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
