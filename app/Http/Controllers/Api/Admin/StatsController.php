<?php

namespace App\Http\Controllers\Api\Admin;


use App\Client;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\LessonsTimeHelper;
use App\Instructor;
use App\Lesson;
use App\SkiSchool\Filters\Admin\StatsFilter;
use App\SkiSchool\Transformers\StatsTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class StatsController extends ApiController
{
    use LessonsTimeHelper;
    /**
     * InstructorController constructor.
     *
     * @param StatsTransformer $transformer
     */
    public function __construct(StatsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get all the instructors.
     *
     * @param StatsFilter $filter
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(StatsFilter $filter)
    {
        $query = Client::query();
        if (!empty($filter->get('from'))) {
            $query = $query->where('created_at', '>=', $filter->get('from'));
        }
        if (!empty($filter->get('to'))) {
            $query = $query->where('created_at', '<=', $filter->get('to'));
        }

        $data['clients'] = $query->count();

        $lessons = Lesson::filter($filter)->get();

        $diff = 0;
        foreach ($lessons as $lesson) {
            $diff = $diff + Carbon::parse($lesson->from)->diffInMinutes(Carbon::parse($lesson->to));
        }

        $data['duration'] = $diff;

        $data['unpaid'] = Lesson::filter($filter)->where('status', '=', 'unpaid')->sum('price');
        $data['paid'] = Lesson::filter($filter)
            ->where('status', '=', 'paid')->sum('price');

        $lesson = Lesson::filter($filter)
            ->select('instructor_id', DB::raw('sum(price) as earned'))
            ->groupBy('instructor_id')
            ->orderBy('earned', 'DESC')
            ->first();

        if (empty($lesson)) {
            $data['best_instructor'] = null;
            $data['best_instructor_duration'] = 0;
        } else {
            $data['best_instructor'] = Instructor::firstWhere('id', '=', $lesson->instructor_id)->name;
            $instructorsLessons = Lesson::filter($filter)
                ->where('instructor_id', '=', $lesson->instructor_id)
                ->get();

            $data['best_instructor_duration'] = $this->getTotalTime($instructorsLessons);
        }

        return $this->respondWithTransformer($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function instructorsStats(StatsFilter $filter)
    {
        $instructors = Instructor::query()->where('email', '<>', 'docasny@instruktor.sk')->get();
        $data = [];
        foreach ($instructors as $instructor) {
            $lessons = Lesson::filter($filter)
                ->where('instructor_id', '=', $instructor->id)
                ->get();

            $diff = 0;
            $total = $lessons->sum('price');
            $minutesByPersons = $this->getTotalTimeByPersons($lessons);
            $data[] = [
                'name' => $instructor->name,
                'duration' => $diff,
                'total' => $total,
                'duration_by_persons' => $minutesByPersons
            ];
        }


        return $this->respondWithTransformer($data);
    }


}
