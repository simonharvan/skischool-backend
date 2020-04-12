<?php

namespace App\Skischool\Transformers;

class StatsTransformer extends Transformer
{
    protected $resourceName = 'stat';

    public function transform($data)
    {
        return $data;
    }
}