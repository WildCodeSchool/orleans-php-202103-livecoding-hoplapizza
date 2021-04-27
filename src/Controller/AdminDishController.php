<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\DishManager;

class AdminDishController extends AbstractController
{
    public const MAX_FIELD_LENGTH = 255;

    public function index(): string
    {
        $dishManager = new DishManager();
        $dishes = $dishManager->selectAllWithCategory();

        return $this->twig->render('Admin/Dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    public function add(): string
    {
        $errors = $dish = [];
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('name', 'DESC');
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $dish = array_map('trim', $_POST);

            $errors = $this->validate($dish);

            if (empty($errors)) {
                // insert en database
                $dishManager = new DishManager();
                $dishManager->insert($dish);

                // redirection
                header('Location: /adminDish/index');
            }
        }
        var_dump($dish);    
        return $this->twig->render('Admin/Dish/add.html.twig', [
            'errors' => $errors,
            'dish' => $dish,
            'categories' => $categories, 
        ]);
    }

    public function edit(int $id): string
    {
        $errors = [];

        $dishManager = new DishManager();
        $dish = $dishManager->selectOneById($id);

        if ($dish === false) {
            $errors[] = 'Le plat sélectionné n\'existe pas';
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $dish = array_map('trim', $_POST);

            $errors = $this->validate($dish);

            if (empty($errors)) {
                // update en database
                $dish['id'] = $id;
                $dishManager->update($dish);

                // redirection
                header('Location: /adminDish/edit/' . $id);
            }
        }

        return $this->twig->render('Admin/Dish/edit.html.twig', [
            'errors' => $errors,
            'dish' => $dish,
        ]);
    }

    private function validate(array $dish): array
    {
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('name', 'DESC');
        $categoryIds = array_column($categories, 'id');
        // foreach($categories as $category) {
        //     $categoryIds[] = $category['id'];
        // }

        $errors = [];
        if (!in_array($dish['category'], $categoryIds)) {
            $errors[] = 'Catégorie incorrecte';
        }

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

        return $errors;
    }

    public function delete(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dishManager = new DishManager();
            $dishManager->delete($id);

            header('Location: /adminDish/index');
        }
    }
}
