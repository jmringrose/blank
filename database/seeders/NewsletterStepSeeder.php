<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsletterStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\NewsletterStep::create([
            'order' => 1,
            'title' => 'Welcome Email',
            'filename' => 'welcome-email.blade.php',
            'draft' => false
        ]);

        \App\Models\NewsletterStep::create([
            'order' => 2,
            'title' => 'Getting Started Guide',
            'filename' => 'getting-started-guide.blade.php',
            'draft' => false
        ]);

        \App\Models\NewsletterStep::create([
            'order' => 3,
            'title' => 'Tips and Tricks',
            'filename' => 'tips-and-tricks.blade.php',
            'draft' => true
        ]);

        \App\Models\NewsletterStep::create([
            'order' => 4,
            'title' => 'Advanced Features',
            'filename' => 'advanced-features.blade.php',
            'draft' => true
        ]);

        \App\Models\NewsletterStep::create([
            'order' => 5,
            'title' => 'Final Thoughts',
            'filename' => 'final-thoughts.blade.php',
            'draft' => true
        ]);
    }
}
