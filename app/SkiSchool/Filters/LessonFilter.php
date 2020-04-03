<?php

namespace App\SkiSchool\Filters;


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

}