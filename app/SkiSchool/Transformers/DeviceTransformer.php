<?php

namespace App\Skischool\Transformers;

use App\SkiSchool\Transformers\Transformer;

class DeviceTransformer extends Transformer
{
    protected $resourceName = 'device';

    public function transform($data)
    {
        return [
            'token' => $data['token'],
            'type' => $data['type'],
        ];
    }
}
