<?php

namespace App\Http\Requests\Api;

class UpdateInstructor extends ApiRequest
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
            'email' => 'required|email',
            'phone' => 'required|string',
            'gender' => 'required|in:male,female',
            'teaching' => 'required|in:ski,snb,both',
            'password' => 'min:6',
        ];
    }
}
