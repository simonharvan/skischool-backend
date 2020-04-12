<?php

namespace App\Http\Requests\Api;

class BulkDeleteAttendance extends ApiRequest
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        return $this->get('attendances') ?: [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'instructor_ids' => 'required|array',
            'date' => 'required|date',
        ];
    }
}
