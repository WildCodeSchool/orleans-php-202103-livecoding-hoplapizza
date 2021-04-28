<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\DishManager;

class AdminDishController extends AbstractController
{
    public const MAX_FIELD_LENGTH = 255;
    public const MAX_UPLOAD_FILESIZE = 100000;
    public const ALLOWED_MIMES = ['image/jpeg', 'image/png'];

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

            $dataErrors = $this->validate($dish);
            $fileErrors = $this->validateFile($_FILES['image']);

            $errors = array_merge($dataErrors, $fileErrors);

            if (empty($errors)) {
                $fileName = uniqid() . '_' . $_FILES['image']['name'];
                $dish['image'] = $fileName;

                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../public/uploads/' .  $fileName);

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

    private function validateFile(array $file): array
    {
        $errors = [];

        if ($file['error'] != 0) {
            $errors[] = 'Problème lors de l\'upload';
        } else {
            if ($file['size'] > self::MAX_UPLOAD_FILESIZE) {
                $errors[] = 'Le fichier doit faire moins de ' . self::MAX_UPLOAD_FILESIZE / 1000000 . 'Mo';
            }

            if (!in_array(mime_content_type($file['tmp_name']), self::ALLOWED_MIMES)) {
                $errors[] = 'Le fichier doit être de type ' . implode(', ', self::ALLOWED_MIMES);
            }
        }

        return $errors;
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

        if (strlen($dish['name']) > self::MAX_FIELD_LENGTH) {
            $errors[] = 'Le champ nom doit faire moins de ' . self::MAX_FIELD_LENGTH . ' caractères';
        }

        return $errors;
    }

    public function delete(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dishManager = new DishManager();
            $dish = $dishManager->selectOneById($id);
            $path =  __DIR__ . '/../../public/uploads/' . $dish['image'];
            if (file_exists($path)) {
                unlink($path);
            }

            $dishManager->delete($id);

            header('Location: /adminDish/index');
        }
    }
}
