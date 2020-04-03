<?php

namespace App\SkiSchool\Filters;


class InstructorFilter extends Filter
{
    /**
     * Filter by instructors name.
     * Get all the instructors by their name
     *
     * @param $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function name($name)
    {
        return $this->builder->where('name', 'LIKE', '%'. $name . '%');
    }

    /**
     * Filter by gender.
     *
     * @param $gender (male/female)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function gender($gender)
    {
        return $this->builder->where('gender', '=', $gender);
    }

    /**
     * Filter by teaching (ski/snb)
     *
     * @param $teaching
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function teaching($teaching)
    {
        if ($teaching === 'both') {
            return $this->builder->where('teaching', '=' ,'both');
        }

        return $this->builder->where(function ($query) use ($teaching) {
            $query->where('teaching', '=', 'both')
                ->orWhere('teaching', '=', $teaching);
        });

    }
}