<?php

namespace App\Validators;

use App\Exceptions\FormValidationException;
use App\Repositories\MysqlTagsRepository;

class AddTagFormValidator implements Validator
{
    private MysqlTagsRepository $tagsRepository;
    private array $errors = [];

    public function __construct()
    {
        $this->tagsRepository = new MysqlTagsRepository();
    }


    public function validate(array $vars): void
    {
        if(trim($_POST['name']) === "")
        {
            $this->errors[] = 'Tag name must not be empty or consist of only spaces!';
        } else {
            $tag = $this->tagsRepository->getOne($_POST['name']);
            if($tag !== null){
                $this->errors[] = "This tag already exists!";
            }
        }
        if(!empty($this->errors)){
            throw new FormValidationException();
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}