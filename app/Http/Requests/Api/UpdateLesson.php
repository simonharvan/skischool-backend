<?php

namespace App\Http\Requests\Api;

class UpdateLesson extends ApiRequest
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        return $this->get('lesson') ?: [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from' => 'required|date',
            'to' => 'required|date',
            'name' => 'string|max:255',
            'type' => 'required|in:ski,snb',
            'price' => 'required|numeric',
            'instructor_id' => 'required|numeric',
        ];
    }
}
