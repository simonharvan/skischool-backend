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
        'from', 'to', 'name', 'type', 'price', 'status', 'instructor_id', 'client_id'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
