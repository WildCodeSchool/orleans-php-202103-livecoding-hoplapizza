<?php

namespace App\Model;

class DishManager extends AbstractManager
{
    public const TABLE = 'dish';

    public function insert(array $dish): void
    {
        $query = "INSERT INTO " . self::TABLE . " (`name`, `description`, `image`)
                  VALUES (:name, :description, :image)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('name', $dish['name'], \PDO::PARAM_STR);
        $statement->bindValue('description', $dish['description'], \PDO::PARAM_STR);
        $statement->bindValue('image', $dish['image'], \PDO::PARAM_STR);

        $statement->execute();
    }
}