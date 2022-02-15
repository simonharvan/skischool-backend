<?php

namespace App;

use App\SkiSchool\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{

    use Filterable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from', 'to', 'name', 'type', 'price', 'persons_count', 'status', 'note', 'instructor_id', 'client_id'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function messages() {
        return $this->hasManyThrough(Message::class, LessonMessage::class, 'lesson_id', 'id', 'id', 'message_id');
    }

    public function changes() {
        return $this->hasMany(LessonChange::class);
    }
}
