<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketingStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\MarketingStep::create([
            'order' => 1,
            'title' => 'Welcome to Photography Marketing',
            'filename' => 'welcome.blade.php',
            'draft' => false
        ]);

        \App\Models\MarketingStep::create([
            'order' => 2,
            'title' => 'Photography Tips & Basics',
            'filename' => 'tips-basics.blade.php',
            'draft' => false
        ]);

        \App\Models\MarketingStep::create([
            'order' => 3,
            'title' => 'Advanced Techniques',
            'filename' => 'advanced-techniques.blade.php',
            'draft' => false
        ]);

        \App\Models\MarketingStep::create([
            'order' => 4,
            'title' => 'Equipment Guide',
            'filename' => 'equipment-guide.blade.php',
            'draft' => false
        ]);

        \App\Models\MarketingStep::create([
            'order' => 5,
            'title' => 'Final Thoughts',
            'filename' => 'final-thoughts.blade.php',
            'draft' => false
        ]);
    }
}
