<?php

namespace App\Http\Controllers\Api\Admin;

use App\Client;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\CreateLesson;
use App\Http\Requests\Api\DeleteLesson;
use App\Http\Requests\Api\PayLessons;
use App\Http\Requests\Api\UpdateLesson;
use App\Instructor;
use App\Lesson;
use App\SkiSchool\Filters\Admin\LessonFilter;
use App\SkiSchool\Notifications\CreatedLesson;
use App\SkiSchool\Transformers\LessonTransformer;
use Illuminate\Database\QueryException;

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
        $lessons = Lesson::filter($filter)->get();

        return $this->respondWithTransformer($lessons);
    }

    /**
     * Create a new lesson and return the lesson if successful.
     *
     * @param CreateLesson $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateLesson $request)
    {
        $lesson = $request->get('lesson');
        $client = $lesson['client'];

        if (!isset($client['id'])) {
            try {
                $client = Client::create([
                    'name' => ucwords($client['name']),
                    'email' => !empty($client['email']) ? $client['email'] : null,
                    'phone' => !empty($client['phone']) ? $client['phone'] : null,
                    'phone_2' => !empty($client['phone_2']) ? $client['phone_2'] : null,
                ]);
            } catch (QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    $query = Client::query();
                    if (!empty($client['email'])) {
                        $query = $query->orWhere('email', '=', $client['email']);
                    }

                    if (!empty($client['phone'])) {
                        $query = $query->orWhere('phone', '=', $client['phone']);
                    }

                    if (!empty($client['phone_2'])) {
                        $query = $query->orWhere('phone_2', '=', $client['phone_2']);
                    }
                    $client = $query->first();
                } else {
                    return $this->respondInternalError('Problem creating client: ' . $e->getMessage());
                }
            }
        } else {
            $client = Client::firstWhere('id', '=', $client['id']);
        }

        if (empty($client)) {
            return $this->respondError('Client not found', 404);
        }

        $instructor = Instructor::firstWhere('id', '=', $lesson['instructor_id']);
        if (empty($instructor)) {
            return $this->respondError('Instructor not found', 404);
        }

        $result = Lesson::create([
            'from' => $lesson['from'],
            'to' => $lesson['to'],
            'name' => !isset($lesson['name']) ? ucwords($client['name']) : ucwords($lesson['name']),
            'type' => $lesson['type'],
            'price' => $lesson['price'],
            'persons_count' => !empty($lesson['persons_count']) ? $lesson['persons_count'] : 1,
            'note' => !empty($lesson['note']) ? $lesson['note'] : null,
            'status' => 'unpaid',
            'instructor_id' => $instructor['id'],
            'client_id' => $client['id']
        ]);

        if ($instructor instanceof Instructor) {
            $instructor->notify(new CreatedLesson($result));
        }

        return $this->respondWithTransformer($result);
    }


    /**
     * Update the lesson given by its slug and return the article if successful.
     *
     * @param UpdateLesson $request
     * @param Lesson $lesson
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateLesson $request, Lesson $lesson)
    {
        $newLesson = $request->get('lesson');

        $instructor = Instructor::firstWhere('id', '=', $newLesson['instructor_id']);

        if (empty($instructor)) {
            return $this->respondError('Instructor not found', 404);
        }

        $newLesson = [
            'from' => $newLesson['from'],
            'to' => $newLesson['to'],
            'price' => $newLesson['price'],
            'type' => $newLesson['type'],
            'note' => $newLesson['note'],
            'instructor_id' => $newLesson['instructor_id'],
        ];

        if (!empty($request->get('lesson')['name'])) {
            $newLesson['name'] = ucwords($request->get('lesson')['name']);
        }

        if (!empty($request->get('lesson')['persons_count'])) {
            $newLesson['persons_count'] = $request->get('lesson')['persons_count'];
        }

        if (!empty($request->get('lesson')['status'])) {
            $newLesson['status'] = $request->get('lesson')['status'];
        }

        $lesson->update($newLesson);

//        if ($instructor instanceof Instructor) {
//            $instructor->notify(new CreatedLesson($lesson));
//        }

        return $this->respondWithTransformer($lesson);
    }

    /**
     * Delete the lesson return the success if successful.
     *
     * @param DeleteLesson $request
     * @param Lesson $lesson
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteLesson $request, Lesson $lesson)
    {
        $lesson->delete();

        return $this->respondSuccess();
    }

    public function preparePay(Lesson $lesson)
    {
        $unpaid = Lesson::query()
            ->where('client_id', '=', $lesson->client_id)
            ->where('status', '=', 'unpaid')
            ->get();

        return $this->respondWithTransformer($unpaid);
    }

    public function pay(PayLessons $request)
    {
        $lessons = $request->get('lesson');
        $update = [
            'status' => 'paid'
        ];

        if (isset($lessons['price'])) {
            $update['price'] = $lessons['price'] / count($lessons['ids']);
        }

        Lesson::whereIn('id', $lessons['ids'])->update($update);

        return $this->respondSuccess();
    }
}
