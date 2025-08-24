<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionStep;

class QuestionStepSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'order' => 1,
                'title' => 'What to Bring on Your Tour',
                'notes' => 'Essential items and packing recommendations for the photography tour',
                'filename' => 'question1.blade.php',
                'draft' => false
            ],
            [
                'order' => 2,
                'title' => 'Customs and Immigration Information',
                'notes' => 'Important details about customs requirements and immigration procedures',
                'filename' => 'question2.blade.php',
                'draft' => false
            ],
            [
                'order' => 3,
                'title' => 'Weather and Clothing Recommendations',
                'notes' => 'Weather expectations and appropriate clothing suggestions',
                'filename' => 'question3.blade.php',
                'draft' => false
            ],
            [
                'order' => 4,
                'title' => 'Photography Equipment Guidelines',
                'notes' => 'Camera equipment recommendations and restrictions',
                'filename' => 'question4.blade.php',
                'draft' => false
            ],
            [
                'order' => 5,
                'title' => 'Travel Insurance and Health Requirements',
                'notes' => 'Insurance requirements and health recommendations for travel',
                'filename' => 'question5.blade.php',
                'draft' => false
            ]
        ];

        foreach ($steps as $step) {
            QuestionStep::create($step);
        }
    }
}