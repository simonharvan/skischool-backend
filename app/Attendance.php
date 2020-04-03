<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
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
