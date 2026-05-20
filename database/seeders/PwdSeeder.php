<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pwd;

class PwdSeeder extends Seeder
{
    public function run(): void
    {
        Pwd::factory()
            ->count(20)
            ->create();
    }
}