<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'phone_2', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function lessons() {
        return $this->hasMany(Lesson::class)->latest();
    }
}
