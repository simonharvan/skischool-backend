<?php

namespace App\Http\Requests\Api;

class CreatePayout extends ApiRequest
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        return $this->get('payout') ?: [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lesson_ids' => 'array|nullable',
            'amount' => 'required|numeric',
            'instructor_id' => 'required|numeric',
        ];
    }
}
