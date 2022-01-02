<?php

namespace App\SkiSchool\Filters\Admin;


use App\SkiSchool\Filters\Filter;

class StatsFilter extends Filter
{

    /**
     * Filter stats by date from
     *
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function from($date)
    {
        return $this->builder->whereDate('from','>=', $date);
    }

    /**
     * Filter by clients name.
     * Get all the articles by the user with given username.
     *
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function to($date)
    {
        return $this->builder->whereDate('to', '<=', $date);
    }
}
