<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\LessonsTimeHelper;
use App\Http\Requests\Api\CreatePayout;
use App\Instructor;
use App\LessonPayout;
use App\Payout;
use App\SkiSchool\Filters\Admin\PayoutFilter;
use App\SkiSchool\Transformers\PayoutTransformer;
use Exception;
use Illuminate\Support\Facades\DB;

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

    /**
     * Create a new payout and return 207 if successful
     *
     * @param CreatePayout $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePayout $request)
    {
        $payout = $request->get('payout');

        $instructor = Instructor::firstWhere('id', '=', $payout['instructor_id']);
        if (empty($instructor)) {
            return $this->respondError('Instructor not found', 404);
        }

        DB::beginTransaction();
        try {
            $result = Payout::create([
                'amount' => $payout['amount'],
                'instructor_id' => $instructor['id'],
            ]);


            if (isset($payout['lesson_ids'])) {
                LessonPayout::insert(
                    array_map(function ($lesson_id) use ($result) {
                        return [
                            'lesson_id' => $lesson_id,
                            'payout_id' => $result['id']
                        ];
                    }, $payout['lesson_ids'])
                );
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->respondError($e->getMessage(), 422);
        }


        if (isset($result)) {
            return $this->respondNoContent();
        } else {
            return $this->respondInternalError("Not possible to create payout");
        }
    }

    private function getLessonsAndStats(Instructor $instructor, PayoutFilter $filter)
    {
        $lessons = $instructor->lessons()->filter($filter)->get();
        $payouts = $instructor->payouts()->get();
        $total = $this->getTotalTime($lessons);
        $stats = [
            'earned_approx' => $total / 60 * 10,
            'total' => $total,
            'total_by_person' => $this->getTotalTimeByPersons($lessons)
        ];

        return [
            'instructor' => $instructor,
            'lessons' => $lessons,
            'stats' => $stats,
            'payouts' => $payouts
        ];
    }
}
