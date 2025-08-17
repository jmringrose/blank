<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\EmailSequence;
use App\Models\MarketingStep;
use App\Mail\MarketingEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class MarketingEmailTest extends TestCase
{
    use DatabaseTransactions;

    public function test_marketing_email_sends_with_correct_template()
    {
        // Create marketing step
        $step = MarketingStep::create([
            'title' => 'Welcome to Photography Tours',
            'filename' => 'welcome.blade.php',
            'order' => 1,
            'draft' => false
        ]);

        // Create marketing sequence
        $sequence = EmailSequence::create([
            'first' => 'John',
            'last' => 'Photographer',
            'email' => 'john@example.com',
            'current_step' => 1,
            'next_send_at' => Carbon::now()->subMinute(),
            'unsub_token' => 'marketing-token'
        ]);

        Mail::fake();

        // Send marketing email
        $mailable = new MarketingEmail($sequence);
        Mail::to($sequence->email)->send($mailable);

        // Assert email was sent
        Mail::assertSent(MarketingEmail::class, function ($mail) use ($sequence) {
            return $mail->hasTo($sequence->email);
        });
    }

    public function test_marketing_command_processes_ready_sequences()
    {
        // Create marketing step
        $step = MarketingStep::create([
            'title' => 'Why Costa Rica Photography',
            'filename' => 'step2.blade.php',
            'order' => 2,
            'draft' => false
        ]);

        // Create ready sequence
        $sequence = EmailSequence::create([
            'first' => 'Jane',
            'last' => 'Photographer',
            'email' => 'jane@example.com',
            'current_step' => 2,
            'next_send_at' => Carbon::now()->subMinute(),
            'unsub_token' => 'marketing-token-2'
        ]);

        Mail::fake();

        // Run marketing command
        $this->artisan('marketing:send')
            ->expectsOutput('Starting marketing email processing...')
            ->assertExitCode(0);

        // Assert sequence was updated
        $sequence->refresh();
        $this->assertEquals(3, $sequence->current_step);
        $this->assertNotNull($sequence->next_send_at);

        Mail::assertSent(MarketingEmail::class);
    }

    public function test_marketing_command_completes_at_step_6()
    {
        // Create marketing step 6
        $step = MarketingStep::create([
            'title' => 'Last Chance - Limited Spots',
            'filename' => 'step6.blade.php',
            'order' => 6,
            'draft' => false
        ]);

        // Create sequence at step 6
        $sequence = EmailSequence::create([
            'first' => 'Bob',
            'last' => 'Photographer',
            'email' => 'bob@example.com',
            'current_step' => 6,
            'next_send_at' => Carbon::now()->subMinute(),
            'unsub_token' => 'marketing-token-3'
        ]);

        Mail::fake();

        // Run marketing command
        $this->artisan('marketing:send')
            ->assertExitCode(0);

        // Assert sequence completed (stays at 6, no further increment)
        $sequence->refresh();
        $this->assertEquals(6, $sequence->current_step);

        Mail::assertSent(MarketingEmail::class);
    }

    public function test_marketing_command_skips_completed_sequences()
    {
        // Create sequence beyond step 6
        $sequence = EmailSequence::create([
            'first' => 'Alice',
            'last' => 'Photographer',
            'email' => 'alice@example.com',
            'current_step' => 7,
            'next_send_at' => Carbon::now()->subMinute(),
            'unsub_token' => 'marketing-token-4'
        ]);

        Mail::fake();

        // Run marketing command
        $this->artisan('marketing:send')
            ->assertExitCode(0);

        // Assert no emails sent for completed sequences
        Mail::assertNotSent(MarketingEmail::class);
    }

    public function test_marketing_email_uses_database_step_title()
    {
        // Create marketing step with specific title
        $step = MarketingStep::create([
            'title' => 'Camera Settings for Costa Rica',
            'filename' => 'step3.blade.php',
            'order' => 3,
            'draft' => false
        ]);

        // Create sequence
        $sequence = EmailSequence::create([
            'first' => 'Test',
            'last' => 'User',
            'email' => 'test@example.com',
            'current_step' => 3,
            'unsub_token' => 'test-token'
        ]);

        // Create mailable
        $mailable = new MarketingEmail($sequence);
        $built = $mailable->build();

        // Assert subject uses database title
        $this->assertEquals('Camera Settings for Costa Rica', $built->subject);
    }

    public function test_marketing_email_fallback_when_no_step_found()
    {
        // Create sequence with step that doesn't exist in database
        $sequence = EmailSequence::create([
            'first' => 'Test',
            'last' => 'User',
            'email' => 'test@example.com',
            'current_step' => 99,
            'unsub_token' => 'test-token'
        ]);

        // Create mailable
        $mailable = new MarketingEmail($sequence);
        $built = $mailable->build();

        // Assert fallback subject is used
        $this->assertEquals('Marketing Email Step 99', $built->subject);
    }
}