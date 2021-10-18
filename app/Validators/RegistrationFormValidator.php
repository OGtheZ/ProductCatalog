<?php

namespace App\Validators;

use App\Repositories\MysqlUsersRepository;
use App\Repositories\UsersRepository;
use App\Exceptions\FormValidationException;

class RegistrationFormValidator implements Validators
{
    private array $errors = [];
    private UsersRepository $usersRepository;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
    }


    /**
     * @throws FormValidationException
     */
    public function validate(array $vars): void // $vars is $_POST
    {
        if($this->usersRepository->getOne($vars['email']) !== null)
        {
            $this->errors[] = "This email is already in use!";
        }

        if ($vars['password'] !== $vars['passwordConfirm'])
        {
            $this->errors[] = "The passwords don't match!";
        }

        if(!empty($this->errors))
        {
            throw new FormValidationException;
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}