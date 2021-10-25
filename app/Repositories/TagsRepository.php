<?php

namespace App\Repositories;

use App\Models\Collections\TagsCollection;
use App\Models\Tag;

interface TagsRepository
{
    public function save(Tag $tag): void;

    public function getAll(): TagsCollection;

    public function assign($productId, $tagId): void;

    public function getProductsTags(string $productId): string;

    public function getOne(string $name): ?Tag;
}