<?php

namespace App\Validators;

use App\Exceptions\FormValidationException;
use App\Repositories\MysqlUsersRepository;
use App\Repositories\UsersRepository;

class LoginFormValidator implements Validators
{
    private array $errors = [];
    private UsersRepository $usersRepository;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
    }

    public function validate(array $vars): void //$vars is $_POST
    {
        if(trim($_POST['email']) === "" || trim($_POST['password']) === ""){
            $this->errors[] = "Name and/or email must not be empty or consist of only spaces!";
        } else {
            $user = $this->usersRepository->getOne($vars['email']);

            if ($user === null) {
                $this->errors[] = 'User not found!';
            }

            if ($user !== null) {
                $passVerified = password_verify($_POST["password"], $user->getPassword());
                if ($passVerified === false) {
                    $this->errors[] = 'Password incorrect!';
                }
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