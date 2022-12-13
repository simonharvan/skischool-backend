<?php

namespace App\SkiSchool\Transformers;

class PayoutTransformer extends Transformer
{
    protected $resourceName = 'payout';

    public function transform($data)
    {
        $lessonTransformer = new LessonTransformer(false, true);
        $instructorTransformer = new InstructorTransformer();

        $lessonData = $data['lessons']->map([$lessonTransformer, 'transform']);

        return [
            'instructor' => $instructorTransformer->transform($data['instructor']),
            'payouts' => $data['payouts']->map([$this, 'transformPayouts']),
            'lessons' => $lessonData,
            'stats' => $data['stats']
        ];
    }

    public function transformPayouts($data)
    {
        return [
            'amount' => $data['amount'],
            'paid_at' => date('Y-m-d H:i:s', strtotime($data['created_at']))
        ];
    }
}
