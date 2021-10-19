<?php

namespace App\Models;

class Tag
{
    private string $id;
    private string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }
}