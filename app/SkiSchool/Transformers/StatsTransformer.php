<?php

namespace App\Skischool\Transformers;

use App\SkiSchool\Transformers\Transformer;

class StatsTransformer extends Transformer
{
    protected $resourceName = 'stat';

    public function transform($data)
    {
        return $data;
    }
}