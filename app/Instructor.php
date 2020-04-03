<?php

namespace App;

use App\SkiSchool\Filters\Filterable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Instructor extends Authenticatable
{

    use Filterable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'gender', 'teaching'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function attendances() {
        return $this->hasMany(Attendance::class)->latest();
    }

    public function lessons() {
        return $this->hasMany(Lesson::class)->latest();
    }

}
