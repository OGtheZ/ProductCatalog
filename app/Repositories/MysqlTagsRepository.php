<?php

namespace App\Repositories;

use App\ConfigGetter;
use App\Models\Collections\ProductsCollection;
use App\Models\Collections\TagsCollection;
use App\Models\Tag;
use PDO;
use PDOException;

class MysqlTagsRepository implements TagsRepository
{
    private PDO $connection;
    private MysqlProductsRepository $productsRepository;

    public function __construct(MysqlProductsRepository $productsRepository)
    {
        $config = ConfigGetter::getConfig();
        $dsn = "mysql:host={$config["host"]};dbname={$config["db"]};charset=UTF8";
        try {
            $this->connection = new PDO($dsn, $config["user"], $config["password"]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
        $this->productsRepository = $productsRepository;
    }

    public function save(Tag $tag): void
    {
        $sql = "INSERT INTO tags (id, name) VALUES (?, ?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$tag->getId(), $tag->getName()]);
    }

    public function getAll(): TagsCollection
    {
        $sql = "SELECT * FROM tags";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        $collection = new TagsCollection();
        foreach ($data as $tag)
        {
            $collection->add(new Tag($tag['id'], $tag['name']));
        }
        return $collection;
    }

    public function assign($productId, $tagId): void
    {
        $sql = "INSERT INTO product_tag (product_id, tag_id) VALUES (?, ?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$productId, $tagId]);
    }

    public function getProductsTags(string $productId): string
    {
        $sql = "SELECT tag_id FROM product_tag WHERE product_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$productId]);
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        $response = [];
        foreach ($data as $row) {
            foreach ($row as $tag) {
                $response[] = $tag;
            }
        }

        $tagNames = '';
        foreach ($response as $tag) {
            $sql = "SELECT name FROM tags WHERE id IN (?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute([$tag]);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($data as $row) {
                foreach($row as $name)
                {
                    $tagNames .= " | $name |";
                }
            }
        }
        return $tagNames;
    }

    public function getOne(string $name): ?Tag
    {
        $sql = "SELECT * FROM tags WHERE name = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$name]);
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if ($data === false){
            return null;
        } else {
            $tag = new Tag($data['id'], $data['name']);
            return $tag;
        }
    }

    public function getTagProducts(string $id): ProductsCollection
    {
        $sql = "SELECT product_id FROM product_tag WHERE tag_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $collection = new ProductsCollection();
        foreach($result as $data)
        {
            foreach($data as $row)
            {
                $product = $this->productsRepository->getOne($row);
                $collection->add($product);
            }
        }
        return $collection;
    }
}