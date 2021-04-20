<?php

namespace App\Controller;

use App\Model\DishManager;

class AdminDishController extends AbstractController
{
    public const MAX_FIELD_LENGTH = 255;

    public function index(): string
    {
        $dishManager = new DishManager();
        $dishes = $dishManager->selectAll();

        return $this->twig->render('Admin/Dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    public function add(): string
    {
        $errors = $dish = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $dish = array_map('trim', $_POST);

            if (empty($dish['name'])) {
                $errors[] = 'Le champ nom est requis';
            }

            if (empty($dish['image'])) {
                $errors[] = 'Le champ image est requis';
            }

            if (strlen($dish['name']) > self::MAX_FIELD_LENGTH) {
                $errors[] = 'Le champ nom doit faire moins de ' . self::MAX_FIELD_LENGTH . ' caractères';
            }

            if (strlen($dish['image']) > self::MAX_FIELD_LENGTH) {
                $errors[] = 'Le champ image doit faire moins de ' . self::MAX_FIELD_LENGTH . ' caractères';
            }

            if (!filter_var($dish['image'], FILTER_VALIDATE_URL)) {
                $errors[] = 'Le champ image doit être une URL';
            }

            if (empty($errors)) {
                // insert en database
                $dishManager = new DishManager();
                $dishManager->insert($dish);

                // redirection
                header('Location: /adminDish/index');
            }
        }

        return $this->twig->render('Admin/Dish/add.html.twig', [
            'errors' => $errors,
            'dish' => $dish,
        ]);
    }
}
