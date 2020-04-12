<?php

namespace App\SkiSchool\Filters;


class AttendanceFilter extends Filter
{
    /**
     * Filter by clients name.
     * Get all the articles by the user with given username.
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function instructor($id)
    {
        return $this->builder->where('instructor_id', '=', $id);
    }

    /**
     * Filter by clients name.
     * Get all the articles by the user with given username.
     *
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function date($date)
    {
        return $this->builder->whereDate('from', $date);
    }

}