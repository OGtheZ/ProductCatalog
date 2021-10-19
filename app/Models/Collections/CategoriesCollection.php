<?php

namespace App\Models\Collections;

use App\Models\Category;

class CategoriesCollection
{
    private array $categories = [];

    public function __construct($categories = [])
    {
        foreach ($categories as $category)
        {
            if($category instanceof Category)
            {
                $this->add($category);
            }
        }
    }

    public function add(Category $category)
    {
        $this->categories[] = $category;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}