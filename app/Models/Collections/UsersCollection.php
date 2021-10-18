<?php

namespace App\Models\Collections;

use App\Models\User;

class UsersCollection
{
    private array $users;

    public function __construct(array $users)
    {
        foreach ($users as $user)
        {
            if($user instanceof User) $this->add($user);
        }
    }

    public function add(User $user)
    {
        $this->users[] = $user;
    }

    public function getUsers(): array
    {
        return $this->users;
    }
}