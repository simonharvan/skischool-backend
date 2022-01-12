<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;

trait LessonsTimeHelper
{

    /**
     * @param $lessons
     * @return int
     */
    protected function getTotalTime($lessons): int
    {
        $total = 0;
        foreach ($lessons as $lesson) {
            $minutes = Carbon::parse($lesson->from)->diffInMinutes(Carbon::parse($lesson->to));
            $total = $total + $minutes;
        }
        return $total;
    }

    /**
     * @param $lessons
     * @return int[]
     */
    protected function getTotalTimeByPersons($lessons): array
    {
        $minutesByPersons = [
            'persons_1' => 0,
            'persons_2' => 0,
            'persons_3' => 0,
            'persons_4' => 0
        ];
        foreach ($lessons as $lesson) {
            $minutes = Carbon::parse($lesson->from)->diffInMinutes(Carbon::parse($lesson->to));
            $minutesByPersons['persons_' . $lesson->persons_count] += $minutes;
        }
        return $minutesByPersons;
    }
}
