<?php

namespace App\Http\Controllers\Api\Instructor;


use App\Device;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\LoginInstructor;
use App\Instructor;
use App\Skischool\Transformers\InstructorTransformer;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\LoginUser;
use App\SkiSchool\Transformers\UserTransformer;

class AuthController extends ApiController
{
    /**
     * AuthController constructor.
     *
     * @param InstructorTransformer $transformer
     */
    public function __construct(InstructorTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Login user and return the user if successful.
     *
     * @param LoginUser $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginInstructor $request)
    {
        $credentials = $request->only('instructor.email', 'instructor.password');
        $credentials = $credentials['instructor'];

        if (! Auth::guard('instructors')->once($credentials)) {
            return $this->respondFailedLogin();
        }

        if (! $token = Auth::guard('instructors')->attempt($credentials)) {
            return $this->respondFailedLogin();
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user instanceof Instructor) {
            $devices = $user->devices();
            foreach ($devices as $device) {
                $device->delete();
            }
        }

        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        return auth()->user();
    }
}
