<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Path to your SQL files
        $sqlPath = database_path('sql');

        $data = File::get($sqlPath . '/data.sql');
        DB::unprepared($data);
        $this->command->info(' Users table seeded successfully.');
    }
}
