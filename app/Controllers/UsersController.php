<?php

namespace App\Controllers;

use App\Models\User;
use App\Repositories\MysqlUsersRepository;
use App\Repositories\UsersRepository;
use App\Services\Users\RegisterUserRequest;
use App\Services\Users\RegisterUserService;
use App\Validators\LoginFormValidator;
use App\Validators\RegistrationFormValidator;
use App\Views\View;
use Carbon\Carbon;
use App\Exceptions\FormValidationException;
use Ramsey\Uuid\Uuid;

class UsersController
{
    private UsersRepository $usersRepository;
    private LoginFormValidator $loginValidator;
    private RegisterUserService $registerUserService;
    private RegistrationFormValidator $registrationFormValidator;

    public function __construct(MysqlUsersRepository $usersRepository,
                                LoginFormValidator $loginValidator,
                                RegisterUserService $registerUserService,
                                RegistrationFormValidator $registrationFormValidator
                                )
    {
        $this->usersRepository = $usersRepository;
        $this->loginValidator = $loginValidator;
        $this->registerUserService = $registerUserService;
        $this->registrationFormValidator = $registrationFormValidator;
    }

    public function login(): View
    {
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
            $this->registrationFormValidator->validate($_POST);
            $request = new RegisterUserRequest($_POST['email'], $_POST['username'], $_POST['password']);
            $this->registerUserService->execute($request);
            header("Location: /");
        } catch (FormValidationException $exception)
        {
            $_SESSION['errors'] = $this->registrationFormValidator->getErrors();
            header("Location: /");
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

