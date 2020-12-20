<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\CreateInstructor;
use App\Http\Requests\Api\DeleteInstructor;
use App\Http\Requests\Api\UpdateInstructor;
use App\Instructor;
use App\SkiSchool\Filters\Admin\InstructorFilter;

use App\Skischool\Transformers\InstructorTransformer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\In;

class InstructorController extends ApiController
{
    /**
     * InstructorController constructor.
     *
     * @param InstructorTransformer $transformer
     */
    public function __construct(InstructorTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get all the instructors.
     *
     * @param InstructorFilter $filter
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(InstructorFilter $filter)
    {
        $instructors = Instructor::filter($filter)->get();

        return $this->respondWithTransformer($instructors);
    }

    /**
     * Create a new article and return the article if successful.
     *
     * @param CreateInstructor $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateInstructor $request)
    {

        $request = $request->get('instructor');
        $instructor = new Instructor();
        $instructor->password = Hash::make($request['password']);
        $instructor->email = $request['email'];
        $instructor->phone = $request['phone'];
        $instructor->name = $request['name'];
        $instructor->teaching = $request['teaching'];
        $instructor->gender = $request['gender'];

        $instructor->save();

        return $this->respondWithTransformer($instructor);
    }


    /**
     * Update the article given by its id and return the article if successful.
     *
     * @param UpdateInstructor $request
     * @param Instructor $instructor
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateInstructor $request, Instructor $instructor)
    {
        if ($request->has('instructor')) {
            $instructor->update($request->get('instructor'));
        }

        return $this->respondWithTransformer($instructor);
    }
//
    /**
     * Delete the instructor given by its id.
     *
     * @param DeleteInstructor $request
     * @param Instructor $instructor
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteInstructor $request, Instructor $instructor)
    {
        $instructor->delete();

        return $this->respondSuccess();
    }
}
