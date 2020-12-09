<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class InstructorDevice extends Model
{
    protected $table = 'instructor_devices';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'instructor_id', 'device_id'
    ];
}
