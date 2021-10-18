<?php

namespace App\Validators;

interface Validators
{
    public function validate(array $vars): void;
}