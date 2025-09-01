<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuestionStep;
use Illuminate\Support\Facades\Mail;

class TestQuestionEmail extends Command
{
    protected $signature = 'email:test-question {step : The question step number} {--email= : Override recipient email}';
    protected $description = 'Send test question email for specified step';

    public function handle()
    {
        $stepNumber = $this->argument('step');
        $email = $this->option('email') ?: env('ADMIN_EMAIL');
        
        $step = QuestionStep::where('order', $stepNumber)->first();
        
        if (!$step) {
            $this->error("Question step {$stepNumber} not found");
            return 1;
        }
        
        if (!$step->filename) {
            $this->error("Question step {$stepNumber} has no template file");
            return 1;
        }
        
        // Create test questioner
        $testQuestioner = (object) [
            'id' => 999,
            'first' => 'Test',
            'last' => 'User',
            'email' => $email
        ];
        
        $unsubscribeUrl = url('/unsubscribe/question/test-token');
        
        try {
            $viewName = 'emails.questions.' . str_replace('.blade.php', '', $step->filename);
            
            Mail::send($viewName, [
                'record' => $testQuestioner,
                'firstName' => $testQuestioner->first,
                'lastName' => $testQuestioner->last,
                'name' => trim($testQuestioner->first . ' ' . $testQuestioner->last),
                'unsubscribeUrl' => $unsubscribeUrl
            ], function ($message) use ($email, $step) {
                $message->to($email)
                        ->subject("Test Question Email - Step {$step->order}: {$step->title}");
            });
            
            // Log the email send for dashboard activity
            \Log::info('Question email sent', [
                'recipient_name' => $testQuestioner->first . ' ' . $testQuestioner->last,
                'recipient_email' => $email,
                'step_number' => $step->order,
                'step_title' => $step->title
            ]);
            
            $this->info("Test question email sent to {$email}");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Failed to send test question email: " . $e->getMessage());
            return 1;
        }
    }
}