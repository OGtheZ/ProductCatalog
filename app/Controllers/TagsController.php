<?php

namespace App\Controllers;

use App\Exceptions\FormValidationException;
use App\Models\Tag;
use App\Repositories\MysqlTagsRepository;
use App\Validators\AddTagFormValidator;
use App\Validators\Validator;
use App\Views\View;
use Ramsey\Uuid\Uuid;

class TagsController
{
    private MysqlTagsRepository $tagsRepository;
    private Validator $addTagFormValidator;

    public function __construct()
    {
        $this->tagsRepository = new MysqlTagsRepository();
        $this->addTagFormValidator = new AddTagFormValidator();
    }

    public function addForm(): View
    {
        $errors = $_SESSION['errors'];
        return new View('/tags/addForm.twig',['errors' => $errors]);
    }

    public function store(): void
    {
        try {
            $this->addTagFormValidator->validate($_POST);
            $tag = new Tag(Uuid::uuid4(), $_POST['name']);
            $this->tagsRepository->save($tag);
            header("Location: /products");
        } catch (FormValidationException $e)
        {
            $_SESSION['errors'] = $this->addTagFormValidator->getErrors();
            header("Location: /tags/create");
            exit;
        }
    }


}