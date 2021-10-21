<?php

namespace App\Repositories;

use App\ConfigGetter;
use App\Models\Category;
use App\Models\Collections\CategoriesCollection;
use PDO;
use PDOException;

class MysqlCategoriesRepository implements CategoriesRepository
{
    private PDO $connection;

    public function __construct()
    {
        $config = ConfigGetter::getConfig();
        $dsn = "mysql:host={$config["host"]};dbname={$config["db"]};charset=UTF8";
        try {
            $this->connection = new PDO($dsn, $config["user"], $config["password"]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function save(Category $category): void
    {
        $sql = "INSERT INTO categories (id , name) VALUES (?, ?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$category->getId(), $category->getName()]);
    }

    public function getAll(): CategoriesCollection
    {
        $categories = new CategoriesCollection();
        $sql = "SELECT * FROM categories";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $row)
        {
            $categories->add(new Category($row['id'], $row['name']));
        }
        return $categories;
    }

    public function getCategoryName($categoryId): string
    {
        $sql = "SELECT name FROM categories WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$categoryId]);
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        return $data['name'];
    }

    public function getOne(string $name): ?Category
    {
        $sql = "SELECT * FROM categories WHERE name = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$name]);
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if ($data === false) return null;
        $category = new Category($data['id'], $data['name']);
        return $category;
    }
}