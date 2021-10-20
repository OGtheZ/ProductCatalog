<?php

namespace App\Models;

use App\Models\Collections\TagsCollection;
use App\Repositories\MysqlTagsRepository;
use Carbon\Carbon;

class Product
{
    private string $name;
    private string $id;
    private string $categoryId;
    private string $categoryName;
    private int $quantity;
    private string $ownerId;
    private string $createdAt;
    private string $editedAt;
    private MysqlTagsRepository $tagsRepository;


    public function __construct(string $name,
                                string $id,
                                string $categoryId,
                                string $categoryName,
                                int $quantity,
                                string $ownerId,
                                ?string $createdAt = null,
                                ?string $editedAt = null
    )
    {
        $this->name = $name;
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->quantity = $quantity;
        $this->ownerId = $ownerId;
        $this->createdAt = $createdAt ?? Carbon::now();
        $this->editedAt = $editedAt ?? Carbon::now();
        $this->tagsRepository = new MysqlTagsRepository();

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getEditedAt(): string
    {
        return $this->editedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getTags()
        // TODO finish this to display tags for each product on /products page
    {
        return $this->tagsRepository->getProductsTags($this->id);
    }
}