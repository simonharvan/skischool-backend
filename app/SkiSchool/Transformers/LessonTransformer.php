<?php

namespace App\Skischool\Transformers;

use App\SkiSchool\Transformers\Transformer;
use App\SkiSchool\Transformers\InstructorTransformer;
use App\SkiSchool\Transformers\ClientTransformer;

class LessonTransformer extends Transformer
{
    protected $resourceName = 'lesson';

    public function transform($data)
    {
        $instructorTransformer = new InstructorTransformer();
        $clientTransformer = new ClientTransformer();
        return [
            'id' => $data['id'],
            'from' => $data['from'],
            'to' => $data['to'],
            'name' => $data['name'],
            'type' => $data['type'],
            'price' => $data['price'],
            'status' => $data['status'],
            'instructor' => $instructorTransformer->transform($data['instructor']),
            'client' => $clientTransformer->transform($data['client']),
        ];
    }
}