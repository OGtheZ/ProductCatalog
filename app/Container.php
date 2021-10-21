<?php

namespace App;

use App\Repositories\CategoriesRepository;
use App\Repositories\MysqlCategoriesRepository;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\MysqlTagsRepository;
use App\Repositories\MysqlUsersRepository;
use App\Repositories\ProductsRepository;
use App\Repositories\TagsRepository;
use App\Repositories\UsersRepository;

class Container
{
    private array $objects;

    public function __construct()
    {
        $this->objects = [
            CategoriesRepository::class => new MysqlCategoriesRepository(),
            ProductsRepository::class => new MysqlProductsRepository(),
            TagsRepository::class => new MysqlTagsRepository(),
            UsersRepository::class => new MysqlUsersRepository()
        ];
    }

    public function save(string $key, $value)
    {
        $this->objects[$key] = $value;
    }

    public function get(string $key)
    {
        return $this->objects[$key];
    }
}