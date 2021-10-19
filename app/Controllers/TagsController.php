<?php

namespace App\Controllers;

use App\Models\Tag;
use App\Repositories\MysqlTagsRepository;
use App\Views\View;
use Ramsey\Uuid\Uuid;

class TagsController
{
    private MysqlTagsRepository $tagsRepository;

    public function __construct()
    {
        $this->tagsRepository = new MysqlTagsRepository();
    }

    public function addForm(): View
    {
        return new View('/tags/addForm.twig');
    }

    public function store(): void
    {
        $tag = new Tag(Uuid::uuid4(), $_POST['name']);
        $this->tagsRepository->save($tag);
        header("Location: /products");
    }
}