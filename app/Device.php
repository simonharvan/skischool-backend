<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'type'
    ];


    /**
     * @return Device
     */
    public function instructor()
    {
        return $this->hasOneThrough(Instructor::class, InstructorDevice::class, 'instructor_id', 'id', 'id', 'id')->first();
    }
}
