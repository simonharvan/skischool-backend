<?php

namespace App;

use App\SkiSchool\Filters\Admin\PayoutFilter;
use App\SkiSchool\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Message extends Model
{

    /**
     * The possible types a message can have.
     */
    const TYPE_CREATED = 'created';
    const TYPE_UPDATED = 'updated';
    const TYPE_ERROR = 'error';

    use Filterable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'text', 'phone', 'created_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function lessons() {
        return $this->hasManyThrough(Lesson::class, LessonMessage::class, 'message_id', 'id', 'id', 'lesson_id');
    }
}
