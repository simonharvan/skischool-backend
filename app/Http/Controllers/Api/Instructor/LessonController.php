<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Api\ApiController;
use App\Lesson;
use App\SkiSchool\Filters\Instructor\LessonFilter;
use App\SkiSchool\Paginate\Paginate;
use App\Skischool\Transformers\LessonTransformer;
use Illuminate\Support\Facades\Auth;

class LessonController extends ApiController
{
    /**
     * LessonController constructor.
     *
     * @param LessonTransformer $transformer
     */
    public function __construct(LessonTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get all the lessons.
     *
     * @param LessonFilter $filter
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(LessonFilter $filter)
    {
        $user = Auth::user();

        $lessons = new Paginate(
            Lesson::filter($filter)
                ->where('instructor_id', '=', $user->id)
                ->orderByDesc('from')
        );

        return $this->respondWithPagination($lessons);
    }
}
