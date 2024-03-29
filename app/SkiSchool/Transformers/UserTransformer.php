<?php

namespace App\SkiSchool\Transformers;

use App\SkiSchool\Transformers\Transformer;

class UserTransformer extends Transformer
{
    protected $resourceName = 'user';

    public function transform($data)
    {
        return [
            'email' => $data['email'],
            'token' => $data['remember_token'],
            'name' => $data['name']
        ];
    }
}
