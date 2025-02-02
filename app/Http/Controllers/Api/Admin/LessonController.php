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
use App\LessonChange;
use App\SkiSchool\Filters\Admin\LessonFilter;
use App\SkiSchool\Notifications\CreatedLesson;
use App\SkiSchool\Transformers\LessonTransformer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

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
            // If the name is Xxx or similar, it is reserved for "free time" client
            if (in_array(strtolower($client['name']), explode(',', env('FREE_TIME_CLIENT_NAME_CHECK', 'xxx')))) {
                $client = $this->queryOrCreateReservedClient();
            } else {
                try {
                    $client = $this->createClient($client);
                } catch (QueryException $e) {
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
            'status' => Lesson::TYPE_UNPAID,
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
        $oldLesson = $lesson->toArray();

        if ($lesson->update($newLesson)) {
            $changes = $lesson->getChanges();
            DB::beginTransaction();
            try {
                foreach ($changes as $key => $change) {
                    if ($key == 'updated_at') {
                        continue;
                    }
                    LessonChange::create([
                        'field' => $key,
                        'old_value' => $oldLesson[$key],
                        'new_value' => $change,
                        'lesson_id' => $lesson['id'],
                        'created_at' => now()
                    ]);
                }
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                return $this->respondError($e->getMessage(), 422);
            }
        }

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
            ->where('status', '=', Lesson::TYPE_UNPAID)
            ->get();

        return $this->respondWithTransformer($unpaid);
    }

    public function pay(PayLessons $request)
    {
        $lessons = $request->get('lesson');
        $update = [
            'status' => Lesson::TYPE_PAID
        ];

        if (isset($lessons['price'])) {
            $update['price'] = $lessons['price'] / count($lessons['ids']);
        }

        Lesson::whereIn('id', $lessons['ids'])->update($update);

        return $this->respondSuccess();
    }

    private function queryOrCreateReservedClient(): Client
    {
        $client = Client::query()
            ->where('name', '=', env('FREE_TIME_CLIENT_NAME', 'Xxx'))
            ->first();
        // First time create
        if (!isset($client)) {
            $client = Client::create([
                'name' => env('FREE_TIME_CLIENT_NAME', 'Xxx'),
                'email' => null,
                'phone' => null,
                'phone_2' => null,
            ]);
        }
        return $client;
    }

    private function createClient($client): Client
    {
        try {
            return Client::create([
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
                return $query->first();
            } else {
                throw $e;
            }
        }
    }
}
