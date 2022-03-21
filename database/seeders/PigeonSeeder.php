<?php

namespace Database\Seeders;

use App\Models\Pigeon;
use Illuminate\Database\Seeder;

class PigeonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pigeon::factory()->count(5)->create();
    }
}
