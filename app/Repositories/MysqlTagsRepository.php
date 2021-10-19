<?php

namespace App\Repositories;

use App\ConfigGetter;
use App\Models\Collections\TagsCollection;
use App\Models\Tag;
use PDO;
use PDOException;

class MysqlTagsRepository
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

    public function getProductsTags($productId)
        // TODO finish this to display products for each tag on /products page
    {
        $sql = "SELECT * FROM product_tag WHERE product_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$productId]);
    }
}