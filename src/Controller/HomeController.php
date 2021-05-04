<?php

namespace App\Controller;

use App\Model\DishManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     */
    public function index()
    {
        return $this->twig->render('Home/index.html.twig', [
            'notification' => $_GET['notification'] ?? '',
        ]);
    }
}
