<?php

namespace App\Validators;

interface Validator
{
    public function validate(array $vars): void;
}