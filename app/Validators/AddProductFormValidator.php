<?php

namespace App\Validators;

use App\Exceptions\FormValidationException;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\ProductsRepository;

class AddProductFormValidator implements Validator
{
    private array $errors = [];
    private ProductsRepository $productsRepository;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
    }

    public function validate(array $vars): void
    {
        if(trim($_POST['name']) === "" || trim($_POST['quantity']) === ""){
            $this->errors[] = "Name and quantity must not be empty or consist of only spaces!";
        } else {
            $product = $this->productsRepository->getOneByName($_POST['name']);

            if ($product !== null) {
                $this->errors[] = 'This product already exists!';
            }

            if (!is_numeric($_POST['quantity'])) {
                $this->errors[] = "Quantity must consist of numbers!";
            }
        }

        if(!empty($this->errors))
        {
            throw new FormValidationException();
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}