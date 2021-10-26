<?php

namespace App\Services\Users;

use App\Exceptions\FormValidationException;
use App\Models\User;
use App\Repositories\MysqlUsersRepository;
use App\Validators\RegistrationFormValidator;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class RegisterUserService
{
    private MysqlUsersRepository $usersRepository;
    private RegistrationFormValidator $registrationFormValidator;

    public function __construct(MysqlUsersRepository $usersRepository,
                                RegistrationFormValidator $registrationFormValidator)
    {
        $this->usersRepository = $usersRepository;

        $this->registrationFormValidator = $registrationFormValidator;
    }

    public function execute(RegisterUserRequest $request)
    {
        $user = new User(
            Uuid::uuid4(),
            $request->getEmail(),
            $request->getUsername(),
            password_hash($request->getPassword(), PASSWORD_DEFAULT),
            Carbon::now()
        );

        $this->usersRepository->save($user);
    }
}
