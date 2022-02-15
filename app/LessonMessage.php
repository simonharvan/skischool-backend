<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class LessonMessage extends Model
{
    protected $table = 'lesson_messages';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lesson_id', 'message_id'
    ];
}
