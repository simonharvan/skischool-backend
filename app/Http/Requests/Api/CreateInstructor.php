<?php

namespace App\Http\Requests\Api;

class CreateInstructor extends ApiRequest
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        return $this->get('instructor') ?: [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:instructors,email',
            'phone' => 'required|string|unique:instructors,phone',
            'gender' => 'required|in:male,female',
            'teaching' => 'required|in:ski,snb,both',
            'password' => 'required|min:6',
        ];
    }
}
