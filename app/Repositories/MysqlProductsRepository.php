<?php

namespace App\Repositories;

use App\ConfigGetter;
use App\Models\Collections\ProductsCollection;
use App\Models\Product;
use Carbon\Carbon;
use PDO;
use PDOException;

class MysqlProductsRepository implements ProductsRepository
{
    private PDO $connection;
    private MysqlCategoriesRepository $categoriesRepository;

    public function __construct()
    {
        $config = ConfigGetter::getConfig();
        $dsn = "mysql:host={$config["host"]};dbname={$config["db"]};charset=UTF8";
        try {
            $this->connection = new PDO($dsn, $config["user"], $config["password"]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
        $this->categoriesRepository = new MysqlCategoriesRepository();
    }

    function getAll(): ProductsCollection
    {
        $sql = "SELECT * FROM products WHERE owner_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$_SESSION['id']]);
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
                $product['owner_id'],
                $product['created_at'],
                $product['edited_at'],

            ));
        }
        return $collection;
    }

    public function getByCategory(string $categoryId): ProductsCollection
    {
        $sql = "SELECT * FROM products WHERE category_id = ? AND owner_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$categoryId, $_SESSION['id']]);
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
                $product['owner_id'],
                $product['created_at'],
                $product['edited_at'],
            ));
        }
        return $collection;
    }

    public function save(Product $product): void
    {
        $sql = "INSERT INTO products (name, id, category_id, category_name, quantity, owner_id, created_at, edited_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $product->getName(),
            $product->getId(),
            $product->getCategoryId(),
            $product->getCategoryName(),
            $product->getQuantity(),
            $product->getOwnerId(),
            $product->getCreatedAt(),
            $product->getEditedAt(),
        ]);
    }

    public function getOne(string $id): Product
    {
        $sql = "SELECT * FROM products WHERE id = ? AND owner_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id, $_SESSION['id']]);
        $product = $statement->fetch();

        return new Product(
            $product['name'],
            $product['id'],
            $product['category_id'],
            $product['category_name'],
            $product['quantity'],
            $product['owner_id'],
            $product['created_at'],
            $product['edited_at'],
        );
    }

    public function edit(Product $product): void
    {
        $sql = "UPDATE products SET name = ?, category_id = ?, category_name = ?, quantity = ?, edited_at = ? Where id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$_POST['name'],
            $_POST['categoryId'],
            $this->categoriesRepository->getCategoryName($_POST['categoryId']),
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
        $sql = "SELECT * FROM products WHERE name = ? AND owner_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$name, $_SESSION['id']]);
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if($data === false) {
            return null;
        } else {
            $product = new Product($data['name'],
                $data['id'],
                $data['category_id'],
                $data['category_name'],
                $data['quantity'],
                $data['owner_id'],
                $data['created_at'],
                $data['edited_at']
            );
            return $product;
        }
    }
}