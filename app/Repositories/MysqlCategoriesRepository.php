<?php

namespace App\Repositories;

use App\Models\Category;
use PDO;
use PDOException;

class MysqlCategoriesRepository
{
    private PDO $connection;

    public function __construct()
    {
        $config = json_decode(file_get_contents("config.json"), true);
        $dsn = "mysql:host={$config["host"]};dbname={$config["db"]};charset=UTF8";
        try {
            $this->connection = new PDO($dsn, $config["user"], $config["password"]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function save(Category $category)
    {
        $sql = "INSERT INTO categories (id , name) VALUES (?, ?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$category->getId(), $category->getName()]);
    }

    public function getAll(): array
    {
        $categories = [];
        $sql = "SELECT * FROM categories";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $row)
        {
            $categories[] = new Category($row['id'], $row['name']);
        }
        return $categories;
    }

    public function getCategoryName($categoryId)
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