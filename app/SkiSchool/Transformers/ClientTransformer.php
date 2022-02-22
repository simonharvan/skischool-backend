<?php

namespace App\SkiSchool\Transformers;

use App\SkiSchool\Transformers\Transformer;

class ClientTransformer extends Transformer
{
    protected $resourceName = 'client';

    public function transform($data)
    {
        return [
            'id' => $data->slug(),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'phone_2' => $data['phone_2'],
        ];
    }
}
