<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsletterSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sequences = [
            [
                'first' => 'John',
                'last' => 'Doe',
                'email' => 'john.doe@example.com',
                'current_step' => 1,
                'unsub_token' => Str::random(12),
                'next_send_at' => Carbon::now()->addDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first' => 'Jane',
                'last' => 'Smith',
                'email' => 'jane.smith@example.com',
                'current_step' => 2,
                'unsub_token' => Str::random(12),
                'next_send_at' => Carbon::now()->addDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first' => 'Bob',
                'last' => 'Johnson',
                'email' => 'bob.johnson@example.com',
                'current_step' => 1,
                'unsub_token' => Str::random(12),
                'next_send_at' => Carbon::now()->addHours(12),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('newsletter_sequences')->insert($sequences);
    }
}