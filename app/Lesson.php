<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from', 'to', 'name', 'type', 'price', 'instructor_id', 'client_id'
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
