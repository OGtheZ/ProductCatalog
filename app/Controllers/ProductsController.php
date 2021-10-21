<?php

namespace App\Controllers;

use App\Container;
use App\Exceptions\FormValidationException;
use App\Models\Product;
use App\Repositories\CategoriesRepository;
use App\Repositories\MysqlCategoriesRepository;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\MysqlTagsRepository;
use App\Repositories\ProductsRepository;
use App\Repositories\TagsRepository;
use App\Validators\AddProductFormValidator;
use App\Validators\Validator;
use App\Views\View;
use Ramsey\Uuid\Uuid;

class ProductsController
{
    private ProductsRepository $productsRepository;
    private Validator $validator;
    private MysqlCategoriesRepository $categoriesRepository;
    private MysqlTagsRepository $tagsRepository;

    public function __construct(Container $container)
    {
        $this->productsRepository = $container->get(ProductsRepository::class);
        $this->validator = new AddProductFormValidator();
        $this->categoriesRepository = $container->get(CategoriesRepository::class);
        $this->tagsRepository = $container->get(TagsRepository::class);
    }

    public function list(): View
    {
        $products = $this->productsRepository->getAll()->getProducts();
        $categories = $this->categoriesRepository->getAll()->getCategories();

        return new View('/products/list.twig', [
            "products" => $products,
            "categories" => $categories
        ]);
    }

    public function addForm(): View
    {
        $errors = $_SESSION['errors'];
        $categories = $this->categoriesRepository->getAll()->getCategories();
        $tags = $this->tagsRepository->getAll()->getTags();

        return new View('/products/addForm.twig', [
            'errors' => $errors,
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    public function store(): void
    {
        try {
            $validator  = $this->validator;
            $validator->validate($_POST);

            $product = new Product(
                $_POST['name'],
                Uuid::uuid4(),
                $_POST['categoryId'],
                $this->categoriesRepository->getCategoryName($_POST['categoryId']),
                $_POST['quantity'],
                $_SESSION['id']
            );
            $tags = array_slice($_POST, 3);

            foreach($tags as $tag)
            {
                $this->tagsRepository->assign($product->getId(), $tag);
            }


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
        if(!isset($_SESSION['id'])){
            header("Location: /");
        }
        $id = $vars['id'] ?? null;
        if ($id == null) header("Location: /products");

        $product = $this->productsRepository->getOne($id);
        $categories = $this->categoriesRepository->getAll()->getCategories();

        return new View('/products/editForm.twig', [
            'product' => $product, 'categories' => $categories
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
        if(!isset($_SESSION['id'])){
            header("Location: /");
        }
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
        if(!isset($_SESSION['id'])){
            header("Location: /");
        }
        $categoryId = $_POST['categoryId'];
        $products = $this->productsRepository->getByCategory($categoryId)->getProducts();
        return new View('/products/categoryView.twig', ["products" => $products]);
    }
}
