<?php

namespace App\SkiSchool\Filters\Admin;


use App\SkiSchool\Filters\Filter;

class ClientFilter extends Filter
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

}