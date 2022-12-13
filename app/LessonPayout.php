<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static insert(array|array[] $array_map)
 */
class LessonPayout extends Model
{
    protected $table = 'lesson_payouts';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lesson_id', 'payout_id'
    ];
}
