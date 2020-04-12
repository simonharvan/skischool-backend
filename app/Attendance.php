<?php

namespace App;

use App\SkiSchool\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use Filterable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from', 'to', 'instructor_id'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
