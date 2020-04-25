<?php

namespace App\Http\Controllers\Api\Admin;


use App\Client;
use App\Http\Controllers\Api\ApiController;
use App\Instructor;
use App\Lesson;
use App\Skischool\Transformers\StatsTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class StatsController extends ApiController
{
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $monthAgo = Carbon::now();
        $monthAgo->subMonth();
        $data['clients'] = Client::query()->where('created_at', '>=', $monthAgo)->count();

        $lessons = Lesson::query()->where('from', '>=', $monthAgo)->get();

        $diff = 0;
        foreach ($lessons as $lesson) {
            $diff = $diff + Carbon::parse($lesson->from)->diffInMinutes(Carbon::parse($lesson->to));
        }

        $data['duration'] = $diff;

        $data['unpaid'] = Lesson::query()->where('status', '=', 'unpaid')->sum('price');
        $data['paid'] = Lesson::query()
            ->where('from', '>=', $monthAgo)
            ->where('status', '=', 'paid')->sum('price');

        $lesson = Lesson::query()
            ->select('instructor_id', DB::raw('sum(price) as earned'))
            ->where('created_at', '>=', $monthAgo)
            ->groupBy('instructor_id')
            ->orderBy('earned', 'DESC')
            ->first();

        $data['best_instructor'] = Instructor::firstWhere('id', '=', $lesson->instructor_id)->name;
        $instructorsLessons = Lesson::query()
            ->where('from', '>=', $monthAgo)
            ->where('instructor_id', '=', $lesson->instructor_id)
            ->get();

        $diff = 0;
        foreach ($instructorsLessons as $lesson) {
            $diff = $diff + Carbon::parse($lesson->from)->diffInMinutes(Carbon::parse($lesson->to));
        }
        $data['best_instructor_duration'] = $diff;

        return $this->respondWithTransformer($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function instructorsStats()
    {
        $monthAgo = Carbon::now();
        $monthAgo->subMonth();

        $instructors = Instructor::query()->where('email', '<>', 'docasny@instruktor.sk')->get();
        $data = [];
        foreach ($instructors as $instructor) {
            $lessons = Lesson::query()
                ->where('from', '>=', $monthAgo)
                ->where('instructor_id', '=', $instructor->id)
                ->get();

            $diff = 0;
            $total = $lessons->sum('price');
            foreach ($lessons as $lesson) {
                $diff = $diff + Carbon::parse($lesson->from)->diffInMinutes(Carbon::parse($lesson->to));
            }
            $data[] = [
                'name' => $instructor->name,
                'duration' => $diff,
                'total' => $total
            ];
        }



        return $this->respondWithTransformer($data);
    }


}
