<?php

namespace App\SkiSchool\Filters\Admin;


use App\SkiSchool\Filters\Filter;

class LessonFilter extends Filter
{
    /**
     * Filter by clients name.
     * Get all the articles by the user with given username.
     *
     * @param $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function name($name)
    {
        return $this->builder->where('name', 'LIKE', '%'. $name . '%');
    }

    /**
     * Filter by clients name.
     * Get all the articles by the user with given username.
     *
     * @param $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function date($date)
    {
        return $this->builder->whereDate('from', $date);
    }

    /**
     * Filter by clients name.
     * Get all the articles by the user with given username.
     *
     * @param $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function status($status)
    {
        return $this->builder->where('status', '=', $status);
    }

    protected function client($id)
    {
        return $this->builder->where('client_id', '=', $id);
    }

}