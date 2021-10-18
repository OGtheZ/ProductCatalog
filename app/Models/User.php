<?php

namespace App\Models;

class User
{
    private string $id;
    private string $email;
    private string $username;
    private string $password;
    private string $createdAt;

    public function __construct(string $id,
                                string $email,
                                string $username,
                                string $password,
                                string $createdAt)
    {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}