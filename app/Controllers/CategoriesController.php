<?php

namespace App\Controllers;

use App\Exceptions\FormValidationException;
use App\Models\Category;
use App\Repositories\MysqlCategoriesRepository;
use App\Validators\AddCategoryFormValidator;
use App\Views\View;
use Ramsey\Uuid\Uuid;

class CategoriesController
{
    private MysqlCategoriesRepository $categoriesRepository;
    private AddCategoryFormValidator $addCategoryFormValidator;

    public function __construct(MysqlCategoriesRepository $categoriesRepository,
                                AddCategoryFormValidator $addCategoryFormValidator)
    {
        $this->categoriesRepository = $categoriesRepository;
        $this->addCategoryFormValidator = $addCategoryFormValidator;
    }

    public function showAddForm(): View
    {
        $errors = $_SESSION['errors'];
        return new View('/categories/addForm.twig', ["errors" => $errors]);
    }

    public function store()
    {
        try {
            $validator = $this->addCategoryFormValidator;
            $validator->validate($_POST);
            $category = new Category(Uuid::uuid4(), $_POST['name']);

            $this->categoriesRepository->save($category);

            header("Location: /products");
        } catch (FormValidationException $e) {
            $_SESSION['errors'] = $validator->getErrors();
            header("Location: /categories/create");
            exit;
        }
    }
}