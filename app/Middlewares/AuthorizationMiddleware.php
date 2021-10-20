<?php

namespace App\Middlewares;

class AuthorizationMiddleware implements Middleware
{
    public function handle(): void
    {
        if (!isset($_SESSION['id'])){
            header("Location: /");
            exit;
        }
    }
}