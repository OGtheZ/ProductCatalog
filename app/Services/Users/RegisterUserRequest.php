<?php

namespace App\Services\Users;

class RegisterUserRequest
{
    private string $email;
    private string $password;
    private string $username;

    public function __construct(string $email, string $username, string $password)
    {
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}