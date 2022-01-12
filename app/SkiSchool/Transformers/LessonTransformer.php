<?php

namespace App\Skischool\Transformers;

use App\SkiSchool\Transformers\Transformer;
use App\SkiSchool\Transformers\InstructorTransformer;
use App\SkiSchool\Transformers\ClientTransformer;

class LessonTransformer extends Transformer
{
    protected $resourceName = 'lesson';

    private $transformAdditional;

    public function __construct($transformAdditional = true)
    {
        $this->transformAdditional = $transformAdditional;
    }

    public function transform($data, $transformInstructor = true, $transformClient = true)
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

        if ($this->transformAdditional) {
            $json['instructor'] = $instructorTransformer->transform($data['instructor']);
            $json['client'] = $clientTransformer->transform($data['client']);
        }
        return $json;
    }
}
