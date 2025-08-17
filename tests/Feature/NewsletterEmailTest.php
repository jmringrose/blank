<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\NewsletterSequence;
use App\Models\NewsletterStep;
use App\Mail\NewsletterEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NewsletterEmailTest extends TestCase
{
    use DatabaseTransactions;

    public function test_newsletter_email_sends_with_correct_template()
    {
        // Create newsletter step
        $step = NewsletterStep::create([
            'title' => 'Test Newsletter',
            'filename' => 'test-newsletter.blade.php',
            'order' => 1,
            'draft' => false
        ]);

        // Create newsletter sequence
        $sequence = NewsletterSequence::create([
            'first' => 'John',
            'last' => 'Doe',
            'email' => 'john@example.com',
            'current_step' => 1,
            'next_send_at' => Carbon::now()->subMinute(),
            'unsub_token' => 'test-token'
        ]);

        // Create email template file
        $templatePath = resource_path('views/emails/newsletters/test-newsletter.blade.php');
        $templateContent = '<!DOCTYPE html><html><body><h1>Test Newsletter</h1><p>Hello {{ $firstName }}!</p></body></html>';
        file_put_contents($templatePath, $templateContent);

        Mail::fake();

        // Send newsletter email
        $mailable = new NewsletterEmail($sequence, $step);
        Mail::to($sequence->email)->send($mailable);

        // Assert email was sent
        Mail::assertSent(NewsletterEmail::class, function ($mail) use ($sequence) {
            return $mail->hasTo($sequence->email);
        });

        // Clean up
        unlink($templatePath);
    }

    public function test_newsletter_command_processes_ready_sequences()
    {
        // Create newsletter step
        $step = NewsletterStep::create([
            'title' => 'Welcome Newsletter',
            'filename' => 'welcome-email.blade.php',
            'order' => 1,
            'draft' => false
        ]);

        // Create ready sequence
        $sequence = NewsletterSequence::create([
            'first' => 'Jane',
            'last' => 'Smith',
            'email' => 'jane@example.com',
            'current_step' => 1,
            'next_send_at' => Carbon::now()->subMinute(),
            'unsub_token' => 'test-token-2'
        ]);

        Mail::fake();

        // Run newsletter command
        $this->artisan('newsletters:send')
            ->expectsOutput('Starting newsletter email processing...')
            ->assertExitCode(0);

        // Assert sequence was updated
        $sequence->refresh();
        $this->assertEquals(2, $sequence->current_step);
        $this->assertNotNull($sequence->next_send_at);

        Mail::assertSent(NewsletterEmail::class);
    }

    public function test_newsletter_command_skips_draft_steps()
    {
        // Create draft newsletter step
        $step = NewsletterStep::create([
            'title' => 'Draft Newsletter',
            'filename' => 'draft-newsletter.blade.php',
            'order' => 1,
            'draft' => true
        ]);

        // Create ready sequence
        $sequence = NewsletterSequence::create([
            'first' => 'Bob',
            'last' => 'Wilson',
            'email' => 'bob@example.com',
            'current_step' => 1,
            'next_send_at' => Carbon::now()->subMinute(),
            'unsub_token' => 'test-token-3'
        ]);

        Mail::fake();

        // Run newsletter command
        $this->artisan('newsletters:send')
            ->assertExitCode(0);

        // Assert sequence was NOT updated (stayed at step 1)
        $sequence->refresh();
        $this->assertEquals(1, $sequence->current_step);

        Mail::assertNotSent(NewsletterEmail::class);
    }

    public function test_newsletter_command_skips_future_sequences()
    {
        // Create newsletter step
        $step = NewsletterStep::create([
            'title' => 'Future Newsletter',
            'filename' => 'future-newsletter.blade.php',
            'order' => 1,
            'draft' => false
        ]);

        // Create future sequence
        $sequence = NewsletterSequence::create([
            'first' => 'Alice',
            'last' => 'Johnson',
            'email' => 'alice@example.com',
            'current_step' => 1,
            'next_send_at' => Carbon::now()->addHour(),
            'unsub_token' => 'test-token-4'
        ]);

        Mail::fake();

        // Run newsletter command
        $this->artisan('newsletters:send')
            ->assertExitCode(0);

        // Assert sequence was NOT updated
        $sequence->refresh();
        $this->assertEquals(1, $sequence->current_step);

        Mail::assertNotSent(NewsletterEmail::class);
    }
}