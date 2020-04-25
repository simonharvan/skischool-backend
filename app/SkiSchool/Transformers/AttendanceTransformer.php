<?php

namespace App\Skischool\Transformers;

use App\SkiSchool\Transformers\Transformer;
use App\SkiSchool\Transformers\InstructorTransformer;

class AttendanceTransformer extends Transformer
{
    protected $resourceName = 'attendance';

    public function transform($data)
    {
        $instructorTransformer = new InstructorTransformer();

        return [
            'id' => $data['id'],
            'from' => $data['from'],
            'to' => $data['to'],
            'instructor' => $instructorTransformer->transform($data['instructor']),
        ];
    }
}