<?php

namespace App\Models;

use Carbon\Carbon;

class Product
{
    private string $name;
    private string $id;
    private string $categoryId;
    private string $categoryName;
    private int $quantity;
    private string $createdAt;
    private string $editedAt;

    public function __construct(string $name,
                                string $id,
                                string $categoryId,
                                string $categoryName,
                                int $quantity,
                                ?string $createdAt = null,
                                ?string $editedAt = null
    )
    {
        $this->name = $name;
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->quantity = $quantity;
        $this->createdAt = $createdAt ?? Carbon::now();
        $this->editedAt = $editedAt ?? Carbon::now();
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
}