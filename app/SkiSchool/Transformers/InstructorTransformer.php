<?php

namespace App\SkiSchool\Transformers;

use App\SkiSchool\Transformers\Transformer;

class InstructorTransformer extends Transformer
{
    protected $resourceName = 'instructor';

    public function transform($data)
    {

        if ($data) {
            return [
                'id' => $data['id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'gender' => $data['gender'],
                'teaching' => $data['teaching'],
            ];
        } else {
            return null;
        }
    }
}
