<?php

namespace App\SkiSchool\Filters\Admin;


use App\SkiSchool\Filters\Filter;

class PayoutFilter extends Filter
{

    /**
     * Filter stats by date from
     *
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function from($date)
    {
        return $this->builder->whereDate('from', '>=', $date);
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

    /**
     * Filter if lesson was already paid to instructor.
     *
     * @param $includePaid
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function includePaid($includePaid)
    {
        if (filter_var($includePaid, FILTER_VALIDATE_BOOLEAN)) {
            return $this->builder;
        } else {
            return $this->builder->whereNotIn('id', function ($query) {
                $query->select('lesson_id')->from('lesson_payouts');
            });
        }
    }
}
