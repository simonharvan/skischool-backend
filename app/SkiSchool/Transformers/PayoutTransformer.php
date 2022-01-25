<?php

namespace App\SkiSchool\Transformers;

use App\SkiSchool\Transformers\Transformer;

class PayoutTransformer extends Transformer
{
    protected $resourceName = 'payout';

    public function transform($data)
    {
        $lessonTransformer = new LessonTransformer(false);
        $instructorTransformer = new InstructorTransformer();

        $lessonData = $data['lessons']->map([$lessonTransformer, 'transform']);

        return [
            'instructor' => $instructorTransformer->transform($data['instructor']),
            'lessons' => $lessonData,
            'stats' => $data['stats']
        ];
    }
}
