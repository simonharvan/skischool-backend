<?php

namespace App\SkiSchool\Transformers;

use App\SkiSchool\Transformers\Transformer;

class ClientLessonTransformer extends Transformer
{
    protected $resourceName = 'data';

    public function transform($data)
    {
        $lessonTransformer = new LessonTransformer(false, true);
        $clientTransformer = new ClientTransformer();
        $lessonData = $data['lessons']->map([$lessonTransformer, 'transform']);

        return [
            'client' => $clientTransformer->transform($data['client']),
            'lessons' => $lessonData
        ];
    }
}
