<?php

namespace App\Repositories;

use App\Models\Collections\ProductsCollection;
use App\Models\Product;
use Carbon\Carbon;
use PDO;
use PDOException;

class MysqlProductsRepository implements ProductsRepository
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

    function getAll(): ProductsCollection
    {
        $sql = "SELECT * FROM products";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);
        $collection = new ProductsCollection();
        foreach($products as $product)
        {
            $collection->add(new Product(
                $product['name'],
                $product['id'],
                $product['category_id'],
                $product['category_name'],
                $product['quantity'],
                $product['created_at'],
                $product['edited_at'],

            ));
        }
        return $collection;
    }

    public function getByCategory(string $categoryId): ProductsCollection
    {
        $sql = "SELECT * FROM products WHERE category_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$categoryId]);
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);
        $collection = new ProductsCollection();
        foreach($products as $product)
        {
            $collection->add(new Product(
                $product['name'],
                $product['id'],
                $product['category_id'],
                $product['category_name'],
                $product['quantity'],
                $product['created_at'],
                $product['edited_at'],
            ));
        }
        return $collection;
    }

    public function save(Product $product): void
    {
        $sql = "INSERT INTO products (name, id, category_id, category_name, quantity, created_at, edited_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $product->getName(),
            $product->getId(),
            $product->getCategoryId(),
            $product->getCategoryName(),
            $product->getQuantity(),
            $product->getCreatedAt(),
            $product->getEditedAt(),
        ]);
    }

    public function getOne(string $id): Product
    {
        $sql = "SELECT * FROM products WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
        $product = $statement->fetch();

        return new Product(
            $product['name'],
            $product['id'],
            $product['category_id'],
            $product['category_name'],
            $product['quantity'],
            $product['created_at'],
            $product['edited_at'],
        );
    }

    public function edit(Product $product): void
    {
        $sql = "UPDATE products SET name = ?, category_id = ?, quantity = ?, edited_at = ? Where id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$_POST['name'],
            $_POST['categoryId'],
            $_POST['quantity'],
            Carbon::now(),
            $product->getId()
            ]);
    }

    public function remove(Product $product)
    {
        $sql = "DELETE FROM products WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$product->getId()]);
    }

    public function getOneByName(string $name): ?Product
    {
        $sql = "SELECT * FROM products WHERE name = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$name]);
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if($data === false) {
            return null;
        } else {
            $product = new Product($data['name'],
                $data['id'],
                $data['category_id'],
                $data['category_name'],
                $data['quantity'],
                $data['created_at'],
                $data['edited_at']
            );
            return $product;
        }
    }
}