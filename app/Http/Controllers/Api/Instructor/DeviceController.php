<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Device;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\CreateDevice;
use App\Instructor;
use App\InstructorDevice;
use App\Lesson;
use App\SkiSchool\Filters\Instructor\LessonFilter;
use App\SkiSchool\Paginate\Paginate;
use App\Skischool\Transformers\DeviceTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;

class DeviceController extends ApiController
{
    /**
     * LessonController constructor.
     *
     * @param DeviceTransformer $transformer
     */
    public function __construct(DeviceTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get all devices.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        if ($user instanceof Instructor) {
            return $this->respondWithTransformer($user->devices()->get());
        }

        return [];
    }

    /**
     * Create a new device and return the device if successful.
     *
     * @param CreateDevice $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateDevice $request)
    {
        $device = $request->get('device');

        $deviceInDb = Device::query()->select()->where('token', '=', $device['token'])->first();

        if (!empty($deviceInDb)) {
            return $this->respondWithTransformer($deviceInDb);
        }

        DB::beginTransaction();
        try {
            $result = Device::create([
                'token' => $device['token'],
                'type' => $device['type'],
            ]);

            InstructorDevice::create([
                'instructor_id' => Auth::id(),
                'device_id' => $result['id']
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondError($e->getMessage(), 422);
        }


        return $this->respondWithTransformer($result);
    }
}
