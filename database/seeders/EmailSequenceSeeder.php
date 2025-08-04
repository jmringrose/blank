<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailSequence;

class EmailSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 records for testing
        EmailSequence::factory()->count(50)->create();
    }
}
