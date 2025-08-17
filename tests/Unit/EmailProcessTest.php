<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\EmailSequence;
use App\Models\NewsletterSequence;
use App\Models\MarketingStep;
use App\Models\NewsletterStep;
use App\Mail\MarketingEmail;
use App\Mail\NewsletterEmail;
use Illuminate\Support\Facades\Mail;

class EmailProcessTest extends TestCase
{
    public function test_marketing_email_mailable_structure()
    {
        // Create a mock sequence
        $sequence = new EmailSequence([
            'first' => 'John',
            'last' => 'Doe',
            'email' => 'john@example.com',
            'current_step' => 1,
            'unsub_token' => 'test-token'
        ]);

        // Create mailable
        $mailable = new MarketingEmail($sequence);

        // Test that mailable has correct properties
        $this->assertEquals($sequence, $mailable->sequence);
        $this->assertEquals(1, $mailable->step);
        $this->assertStringContainsString('test-token', $mailable->unsubscribeUrl);
    }

    public function test_newsletter_email_mailable_structure()
    {
        // Create mock sequence and step
        $sequence = new NewsletterSequence([
            'first' => 'Jane',
            'last' => 'Smith',
            'email' => 'jane@example.com',
            'current_step' => 1,
            'unsub_token' => 'newsletter-token'
        ]);

        $step = new NewsletterStep([
            'title' => 'Welcome Newsletter',
            'filename' => 'welcome-email.blade.php',
            'order' => 1,
            'draft' => false
        ]);

        // Create mailable
        $mailable = new NewsletterEmail($sequence, $step);

        // Test that mailable has correct properties
        $this->assertEquals($sequence, $mailable->sequence);
        $this->assertEquals($step, $mailable->step);
        $this->assertStringContainsString('newsletter-token', $mailable->unsubscribeUrl);
    }

    public function test_marketing_email_unsubscribe_url_format()
    {
        $sequence = new EmailSequence([
            'first' => 'Test',
            'last' => 'User',
            'email' => 'test@example.com',
            'current_step' => 2,
            'unsub_token' => 'unique-token-123'
        ]);

        $mailable = new MarketingEmail($sequence);

        $expectedUrl = url('/unsubscribe?token=unique-token-123');
        $this->assertEquals($expectedUrl, $mailable->unsubscribeUrl);
    }

    public function test_newsletter_email_unsubscribe_url_format()
    {
        $sequence = new NewsletterSequence([
            'first' => 'Test',
            'last' => 'User',
            'email' => 'test@example.com',
            'current_step' => 1,
            'unsub_token' => 'newsletter-token-456'
        ]);

        $step = new NewsletterStep([
            'title' => 'Test Newsletter',
            'filename' => 'test.blade.php',
            'order' => 1,
            'draft' => false
        ]);

        $mailable = new NewsletterEmail($sequence, $step);

        $expectedUrl = url('/unsubscribe?token=newsletter-token-456');
        $this->assertEquals($expectedUrl, $mailable->unsubscribeUrl);
    }

    public function test_marketing_command_class_exists()
    {
        // Test that the marketing command class exists
        $this->assertTrue(class_exists('App\Console\Commands\marketingEmails'));
    }

    public function test_newsletter_command_class_exists()
    {
        // Test that the newsletter command class exists
        $this->assertTrue(class_exists('App\Console\Commands\NewsletterEmails'));
    }

    public function test_marketing_email_step_increment_logic()
    {
        // Test step 1 to 6 progression
        for ($step = 1; $step <= 6; $step++) {
            $sequence = new EmailSequence([
                'first' => 'Test',
                'last' => 'User',
                'email' => 'test@example.com',
                'current_step' => $step,
                'unsub_token' => 'token'
            ]);

            $mailable = new MarketingEmail($sequence);
            $this->assertEquals($step, $mailable->step);
        }
    }

    public function test_newsletter_step_draft_property()
    {
        // Test draft vs published steps
        $draftStep = new NewsletterStep([
            'title' => 'Draft Newsletter',
            'filename' => 'draft.blade.php',
            'order' => 1,
            'draft' => true
        ]);

        $publishedStep = new NewsletterStep([
            'title' => 'Published Newsletter',
            'filename' => 'published.blade.php',
            'order' => 2,
            'draft' => false
        ]);

        $this->assertTrue($draftStep->draft);
        $this->assertFalse($publishedStep->draft);
    }
}