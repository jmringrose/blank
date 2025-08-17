<?php

namespace App\Http\Controllers;

use App\Models\EmailSequence;
use App\Models\NewsletterSequence;
use App\Models\NewsletterStep;
use App\Models\MarketingStep;
use App\Mail\MarketingEmail;
use App\Mail\NewsletterEmail;
use Illuminate\Http\Request;

class EmailPreviewController extends Controller
{
    public function marketing($step = 1)
    {
        $marketingStep = MarketingStep::where('order', $step)->first();
        
        if (!$marketingStep) {
            return response('Marketing step not found', 404);
        }
        
        $sequence = EmailSequence::first() ?? new EmailSequence([
            'first' => 'John',
            'last' => 'Doe',
            'email' => 'john@example.com',
            'current_step' => $step,
            'unsub_token' => 'preview-token'
        ]);
        
        $sequence->current_step = $step;
        
        $mailable = new MarketingEmail($sequence);
        return $mailable->render();
    }
    
    public function newsletter($stepOrder = 1)
    {
        $sequence = NewsletterSequence::first() ?? new NewsletterSequence([
            'first' => 'Jane',
            'last' => 'Smith',
            'email' => 'jane@example.com',
            'current_step' => $stepOrder,
            'unsub_token' => 'preview-token'
        ]);
        
        $step = NewsletterStep::where('order', $stepOrder)->where('draft', false)->first();
        
        if (!$step) {
            return response('Newsletter step not found', 404);
        }
        
        $mailable = new NewsletterEmail($sequence, $step);
        return $mailable->render();
    }
}