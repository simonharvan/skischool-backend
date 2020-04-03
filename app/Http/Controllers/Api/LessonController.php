<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\Requests\Api\CreateLesson;
use App\Http\Requests\Api\DeleteLesson;
use App\Http\Requests\Api\UpdateLesson;
use App\Instructor;
use App\Lesson;
use App\SkiSchool\Filters\InstructorFilter;
use App\SkiSchool\Filters\LessonFilter;
use App\Skischool\Transformers\LessonTransformer;
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
     * @param InstructorFilter $filter
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(LessonFilter $filter)
    {
        $instructors = Lesson::filter($filter)->get();

        return $this->respondWithTransformer($instructors);
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
        if (!isset($client->id)) {
            try {
                $client = Client::create([
                    'name' => $client['name'],
                    'email' => $client['email'],
                    'phone' => $client['phone'],
                    'phone_2' => !empty($client['phone_2']) ? $client['phone_2'] : null,
                ]);
            } catch (QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    $client = Client::where('email', '=', $client['email'])->orWhere('phone', '=', $client['phone'])->first();
                } else {
                    return $this->respondInternalError('Problem creating client: ' . $e->getMessage());
                }
            }
        }
        $instructor = Instructor::firstWhere('id', '=', $lesson['instructor_id']);
        if (empty($instructor)) {
            return $this->respondError('Instructor not found', 404);
        }

        $result = Lesson::create([
            'from' => $lesson['from'],
            'to' => $lesson['to'],
            'name' => !empty($lesson['name']) ? $lesson['name'] : $client->name,
            'type' => $lesson['type'],
            'price' => $lesson['price'],
            'instructor_id' => $instructor->id,
            'client_id' => $client->id
        ]);

        return $this->respondWithTransformer($result);
    }


    /**
     * Update the lesson given by its slug and return the article if successful.
     *
     * @param UpdateLesson $request
     * @param Lesson $article
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
            'to' => $newLesson['from'],
            'price' => $newLesson['price'],
            'type' => $newLesson['type'],
            'instructor_id' => $newLesson['instructor_id'],
        ];

        if (!empty($request->get('lesson')['name'])) {
            $newLesson['name'] = $request->get('lesson')['name'];
        }

        $lesson->update($newLesson);

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
//
//    /**
//     * Delete the article given by its slug.
//     *
//     * @param DeleteArticle $request
//     * @param Article $article
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function destroy(DeleteArticle $request, Article $article)
//    {
//        $article->delete();
//
//        return $this->respondSuccess();
//    }
}
