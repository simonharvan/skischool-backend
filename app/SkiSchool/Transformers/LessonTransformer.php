<?php

namespace App\SkiSchool\Transformers;

use App\SkiSchool\Transformers\Transformer;
use App\SkiSchool\Transformers\InstructorTransformer;
use App\SkiSchool\Transformers\ClientTransformer;

class LessonTransformer extends Transformer
{
    protected $resourceName = 'lesson';

    private $addClientAndInstructor;
    private $addPaidToInstructor;

    public function __construct($addClientAndInstructor = true, $addPaidToInstructor = false)
    {
        $this->addClientAndInstructor = $addClientAndInstructor;
        $this->addPaidToInstructor = $addPaidToInstructor;
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

        if ($this->addClientAndInstructor) {
            $json['instructor'] = $instructorTransformer->transform($data['instructor']);
            $json['client'] = $clientTransformer->transform($data['client']);
        }

        if ($this->addPaidToInstructor) {
            $json['instructor_paid'] = $data['payout'] !== null;
        }
        return $json;
    }
}
