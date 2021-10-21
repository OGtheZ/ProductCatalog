<?php

namespace App\Repositories;

use App\Models\Collections\ProductsCollection;
use App\Models\Product;

interface ProductsRepository
{
    public function getAll(): ProductsCollection;

    public function getByCategory(string $category): ProductsCollection;

    public function save(Product $product): void;

    public function getOne(string $id): Product;

    public function edit(Product $product): void;

    public function remove(Product $product): void;

    public function getOneByName(string $name): ?Product;
}