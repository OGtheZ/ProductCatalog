<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Collections\CategoriesCollection;

interface CategoriesRepository
{
    public function save(Category $category): void;

    public function getAll(): CategoriesCollection;

    public function getCategoryName($categoryId): string;

    public function getOne(string $name): ?Category;
}