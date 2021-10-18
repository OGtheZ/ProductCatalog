<?php

namespace App\Controllers;

use App\Models\User;
use App\Repositories\MysqlUsersRepository;
use App\Repositories\UsersRepository;
use App\Validators\LoginFormValidator;
use App\Validators\RegistrationFormValidator;
use App\Views\View;
use Carbon\Carbon;
use App\Exceptions\FormValidationException;
use Ramsey\Uuid\Uuid;

class UsersController
{
    private UsersRepository $usersRepository;
    private RegistrationFormValidator $regValidator;
    private LoginFormValidator $loginValidator;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
        $this->regValidator = new RegistrationFormValidator();
        $this->loginValidator = new LoginFormValidator();
    }

    public function login(): View
    {
        if(isset($_SESSION['id'])){
            header("Location: /products");
            exit;
        }
        $errors = $_SESSION['errors'];
        return new View('/users/login.twig', ['errors' => $errors]);
    }

    public function showRegisterForm(): View
    {
            $errors = $_SESSION['errors'];
            return new View('/users/register.twig', ['errors' => $errors]);
    }

    public function register()
    {
        try {
            $this->regValidator->validate($_POST);

            $user = new User(
                Uuid::uuid4(),
                $_POST['email'],
                $_POST['username'],
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                Carbon::now()
            );

            $this->usersRepository->save($user);

            header("Location: /");
        } catch (FormValidationException $e) {
            $_SESSION['errors'] = $this->regValidator->getErrors();
            header("Location: /register");
            exit;
        }
    }

    public function authorize(): void
    {
        try {
            $this->loginValidator->validate($_POST);
            $user = $this->usersRepository->getOne($_POST['email']);
            $_SESSION['id'] = $user->getId();
            header("Location: /products");
        } catch (FormValidationException $e) {
            $_SESSION['errors'] = $this->loginValidator->getErrors();
            header("Location: /");
            exit;
        }
    }



    public function logout()
    {
        unset($_SESSION['id']);
        header("Location: /");
    }
}

