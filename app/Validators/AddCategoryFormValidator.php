<?php

namespace App\Validators;

use App\Exceptions\FormValidationException;
use App\Repositories\MysqlCategoriesRepository;

class AddCategoryFormValidator implements Validators
{
    private array $errors = [];
    private MysqlCategoriesRepository $categoriesRepository;

    public function __construct()
    {
        $this->categoriesRepository = new MysqlCategoriesRepository();
    }

    public function validate(array $vars): void
    {
        if(trim($_POST['name']) === ""){
            $this->errors[] = "Name must not consist of only spaces or be empty!";
        } else {
            $category = $this->categoriesRepository->getOne($_POST['name']);
            if ($category !== null) {
                $this->errors[] = "This category already exists!";
            }
        }
        if(!empty($this->errors)){
            throw new FormValidationException();
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}