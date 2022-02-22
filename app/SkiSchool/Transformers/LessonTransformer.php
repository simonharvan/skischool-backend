<?php

namespace App\SkiSchool\Transformers;

use App\SkiSchool\Transformers\Transformer;
use App\SkiSchool\Transformers\InstructorTransformer;
use App\SkiSchool\Transformers\ClientTransformer;

class LessonTransformer extends Transformer
{
    protected $resourceName = 'lesson';

    private $transformClient;
    private $transformInstructor;

    public function __construct($transformClient = true, $transformInstructor = true)
    {
        $this->transformClient = $transformClient;
        $this->transformInstructor = $transformInstructor;
    }

    public function transform($data)
    {
        $instructorTransformer = new InstructorTransformer();
        $clientTransformer = new ClientTransformer();

        $json = [
            'id' => $data['id'],
            'from' => $data['from'],
            'to' => $data['to'],
            'name' => $data['name'],
            'type' => $data['type'],
            'price' => $data['price'],
            'status' => $data['status'],
            'persons_count' => $data['persons_count'],
            'note' => $data['note']
        ];

        if ($this->transformClient) {
            $json['client'] = $clientTransformer->transform($data['client']);
        }
        if ($this->transformInstructor) {
            $json['instructor'] = $instructorTransformer->transform($data['instructor']);
        }
        return $json;
    }
}
