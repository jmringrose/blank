<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionSequence;
use App\Models\QuestionStep;
use Illuminate\Support\Str;

class QuestionSequenceSeeder extends Seeder
{
    public function run(): void
    {
        $questionSteps = QuestionStep::all();
        
        if ($questionSteps->isEmpty()) {
            $this->command->info('No question steps found. Please run QuestionStepSeeder first.');
            return;
        }

        $sequences = [
            [
                'first' => 'John',
                'last' => 'Smith',
                'email' => 'john.smith@example.com',
                'question_step_id' => $questionSteps->where('order', 1)->first()->id,
                'sent' => false,
                'unsub_token' => Str::random(32)
            ],
            [
                'first' => 'Sarah',
                'last' => 'Johnson',
                'email' => 'sarah.johnson@example.com',
                'question_step_id' => $questionSteps->where('order', 2)->first()->id,
                'sent' => false,
                'unsub_token' => Str::random(32)
            ],
            [
                'first' => 'Mike',
                'last' => 'Davis',
                'email' => 'mike.davis@example.com',
                'question_step_id' => $questionSteps->where('order', 3)->first()->id,
                'sent' => true,
                'unsub_token' => Str::random(32)
            ],
            [
                'first' => 'Emily',
                'last' => 'Wilson',
                'email' => 'emily.wilson@example.com',
                'question_step_id' => $questionSteps->where('order', 4)->first()->id,
                'sent' => false,
                'unsub_token' => Str::random(32)
            ],
            [
                'first' => 'David',
                'last' => 'Brown',
                'email' => 'david.brown@example.com',
                'question_step_id' => $questionSteps->where('order', 1)->first()->id,
                'sent' => true,
                'unsub_token' => Str::random(32)
            ]
        ];

        foreach ($sequences as $sequence) {
            QuestionSequence::create($sequence);
        }
    }
}