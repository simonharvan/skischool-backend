<?php

namespace App\Http\Controllers\Api\Admin;

use App\Attendance;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\BulkCreateAttendance;
use App\Http\Requests\Api\BulkDeleteAttendance;
use App\Http\Requests\Api\CreateAttendance;
use App\Http\Requests\Api\CreateLesson;
use App\Http\Requests\Api\DeleteAttendance;
use App\Http\Requests\Api\DeleteInstructor;
use App\Http\Requests\Api\UpdateAttendance;
use App\Http\Requests\Api\UpdateLesson;
use App\Instructor;

use App\SkiSchool\Filters\Admin\AttendanceFilter;
use App\SkiSchool\Transformers\AttendanceTransformer;

class AttendanceController extends ApiController
{
    /**
     * LessonController constructor.
     *
     * @param AttendanceTransformer $transformer
     */
    public function __construct(AttendanceTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get all the lessons.
     *
     * @param AttendanceFilter $filter
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(AttendanceFilter $filter)
    {
        $attendace = Attendance::filter($filter)->get();

        return $this->respondWithTransformer($attendace);
    }

    /**
     * Create a new attendance and return the attendance if successful.
     *
     * @param CreateAttendance $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateAttendance $request)
    {
        $attendance = $request->get('attendance');

        $instructor = Instructor::firstWhere('id', '=', $attendance['instructor_id']);
        if (empty($instructor)) {
            return $this->respondError('Instructor not found', 404);
        }

        $result = Attendance::create([
            'from' => $attendance['from'],
            'to' => $attendance['to'],
            'instructor_id' => $instructor['id'],
        ]);

        return $this->respondWithTransformer($result);
    }

    /**
     * Create a new lesson and return the lesson if successful.
     *
     * @param CreateLesson $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkStore(BulkCreateAttendance $request)
    {
        $attendances = $request->get('attendances');
        $result = [];
        $from = substr($attendances['from'],0, 10);
        foreach ($attendances['instructor_ids'] as $instructor_id) {
            $instructor = Instructor::firstWhere('id', '=', $instructor_id);
            if (empty($instructor)) {
                return $this->respondError('Instructor with id ' . $instructor_id . ' not found', 404);
            }

            $attendance = Attendance::query()->where('instructor_id', '=', $instructor_id)->whereDate('from', '=', $from)->first();

            if (!empty($attendance)) {
                continue;
            }

            $tmp = Attendance::create([
                'from' => $attendances['from'],
                'to' => $attendances['to'],
                'instructor_id' => $instructor_id,
            ]);
            array_push($result, $tmp);
        }

        if (count($result) === 0) {
            return $this->respondSuccess();
        }
        return $this->respondWithTransformer($result);
    }




    /**
     * Update the lesson given by its slug and return the article if successful.
     *
     * @param UpdateLesson $request
     * @param Attendance $attendance
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateAttendance $request, Attendance $attendance)
    {
        $newAttendance = $request->get('attendance');

        $instructor = Instructor::firstWhere('id', '=', $newAttendance['instructor_id']);

        if (empty($instructor)) {
            return $this->respondError('Instructor not found', 404);
        }

        $attendance->update($newAttendance);

        return $this->respondWithTransformer($attendance);
    }

    /**
     * Delete the lesson return the success if successful.
     *
     * @param DeleteInstructor $request
     * @param Lesson $lesson
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteAttendance $request, Attendance $attendance)
    {
        $attendance->delete();

        return $this->respondSuccess();
    }

    public function bulkDestroy(BulkDeleteAttendance $request)
    {
        $attendances = $request->get('attendances');

        $date = substr($attendances['date'],0, 10);
        foreach ($attendances['instructor_ids'] as $instructor_id) {
            $instructor = Instructor::firstWhere('id', '=', $instructor_id);
            if (empty($instructor)) {
                return $this->respondError('Instructor with id ' . $instructor_id . ' not found', 404);
            }

            $attendance = Attendance::query()->where('instructor_id', '=', $instructor_id)->whereDate('from', '=', $date)->first();

            if (empty($attendance)) {
                continue;
//                return $this->respondError('Attendance with date '. $date .' and instructor ' . $instructor_id . ' not found', 404);
            }

            $attendance->delete();
        }

        return $this->respondSuccess();

    }
}
