<?php

namespace Database;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    protected $totalUsers = 1;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\User::class)->times($this->totalUsers)->create();
    }
}
