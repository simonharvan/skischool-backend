<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\LessonsTimeHelper;
use App\Http\Requests\Api\CreateInstructor;
use App\Http\Requests\Api\DeleteInstructor;
use App\Http\Requests\Api\UpdateInstructor;
use App\Instructor;
use App\SkiSchool\Filters\Admin\InstructorFilter;

use App\SkiSchool\Filters\Admin\PayoutFilter;
use App\SkiSchool\Transformers\InstructorTransformer;
use App\SkiSchool\Transformers\PayoutTransformer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\In;

class PayoutController extends ApiController
{
    use LessonsTimeHelper;

    /**
     * PayoutController constructor.
     *
     * @param PayoutTransformer $transformer
     */
    public function __construct(PayoutTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get lessons by instructor
     *
     * @param PayoutFilter $filter
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PayoutFilter $filter)
    {
        $data = [];
        $instructors = Instructor::where('email', '<>', 'docasny@instruktor.sk')->get();
        foreach ($instructors as $instructor) {
            $data[] = $this->getLessonsAndStats($instructor, $filter);
        }
        return $this->respondWithTransformer($data);
    }

    private function getLessonsAndStats(Instructor $instructor, PayoutFilter $filter)
    {

        $lessons = $instructor->lessons()->filter($filter)->get();
        $total = $this->getTotalTime($lessons);
        $stats = [
            'earned_approx' => $total / 60 * 10,
            'total' => $total,
            'total_by_person' => $this->getTotalTimeByPersons($lessons)
        ];

        return [
            'instructor' => $instructor,
            'lessons' => $lessons,
            'stats' => $stats
        ];
    }
}
