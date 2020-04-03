<?php

namespace App\Skischool\Transformers;

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
            'instructor' => $instructorTransformer->transform($data['instructor']),
            'client' => $clientTransformer->transform($data['client']),
        ];
    }
}