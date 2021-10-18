<?php

namespace App\Controllers;

use App\Exceptions\FormValidationException;
use App\Models\Product;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\ProductsRepository;
use App\Validators\AddProductFormValidator;
use App\Validators\Validators;
use App\Views\View;
use Ramsey\Uuid\Uuid;

class ProductsController
{
    private ProductsRepository $productsRepository;
    private Validators $validator;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
        $this->validator = new AddProductFormValidator();
    }

    public function list(): View
    {
        $products = $this->productsRepository->getAll()->getProducts();

        return new View('/products/list.twig', [
            "products" => $products
        ]);
    }

    public function addForm(): View
    {
        $errors = $_SESSION['errors'];
        return new View('/products/addForm.twig', ['errors' => $errors]);
    }

    public function store(): void
    {
        try {
            $validator  = $this->validator;
            $validator->validate($_POST);
            $product = new Product($_POST['name'], Uuid::uuid4(), $_POST['categoryId'],
                $this->productsRepository->getCategoryName($_POST['categoryId']), $_POST['quantity']);

            $this->productsRepository->save($product);

            header("Location: /products");
            } catch (FormValidationException $e)
        {
            $_SESSION['errors'] = $validator->getErrors();
            header("Location: /products/create");
            exit;
        }
    }

    public function editForm(array $vars): View
    {
        $id = $vars['id'] ?? null;
        if ($id == null) header("Location: /products");

        $product = $this->productsRepository->getOne($id);

        return new View('/products/editForm.twig', [
            'product' => $product
        ]);
    }

    public function edit(array $vars): void
    {
        $id = $vars['id'] ?? null;
        if ($id == null) header("Location: /products");
        $product = $this->productsRepository->getOne($id);

        $this->productsRepository->edit($product);
        header("Location: /products");
    }

    public function removeConfirmation(array $vars): view
    {
        $id = $vars['id'] ?? null;

        $product = $this->productsRepository->getOne($id);

        return new View('/products/remove.twig', ['product' => $product]);
    }

    public function remove(array $vars): void
    {
        $id = $vars['id'] ?? null;
        $product = $this->productsRepository->getOne($id);

        $this->productsRepository->remove($product);

        header("Location: /products");
    }

    public function searchByCategory(): View
    {
        $categoryId = $_POST['categoryId'];
        $products = $this->productsRepository->getByCategory($categoryId)->getProducts();
        return new View('/products/categoryView.twig', ["products" => $products]);
    }
}
