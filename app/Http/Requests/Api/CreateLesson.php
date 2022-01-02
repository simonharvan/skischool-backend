<?php

namespace App\Http\Requests\Api;

class CreateLesson extends ApiRequest
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
            'persons_count' => 'numeric|min:1',
            'note' => 'string',
            'client.id' => 'required_without:client.name|numeric',
            'client.name' => 'required_without:client.id|string',
            'client.email' => 'email',
            'client.phone' => 'string',
            'client.phone_2' => 'string',
            'instructor_id' => 'required|numeric',
        ];
    }
}
