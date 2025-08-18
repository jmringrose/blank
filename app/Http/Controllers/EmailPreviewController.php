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

        $sequence = EmailSequence::first();
        if (!$sequence) {
            $sequence = new EmailSequence([
                'first' => 'John',
                'last' => 'Doe',
                'email' => 'john@example.com',
                'current_step' => $step,
                'unsub_token' => 'preview-token'
            ]);
        }

        $sequence->current_step = $step;
        $sequence->name = ($sequence->first ?? 'John') . ' ' . ($sequence->last ?? 'Doe');

        $mailable = new MarketingEmail($sequence);
        return $mailable->render();
    }

    public function newsletter($stepOrder = 1)
    {
        $sequence = NewsletterSequence::first();
        if (!$sequence) {
            $sequence = new NewsletterSequence([
                'first' => 'Jane',
                'last' => 'Smith',
                'email' => 'jane@example.com',
                'current_step' => $stepOrder,
                'unsub_token' => 'preview-token'
            ]);
        } else {
            $sequence->current_step = $stepOrder;
            $sequence->first = $sequence->first ?? 'Jane';
            $sequence->last = $sequence->last ?? 'Smith';
            $sequence->email = $sequence->email ?? 'jane@example.com';
        }
        $sequence->name = ($sequence->first ?? 'Jane') . ' ' . ($sequence->last ?? 'Smith');

        $step = NewsletterStep::where('order', $stepOrder)->first();

        if (!$step) {
            return response('Newsletter step not found', 404);
        }

        if ($step->draft) {
            return response(view('emails.draft-message', [
                'stepTitle' => $step->title,
                'stepOrder' => $stepOrder
            ]), 200);
        }

        $mailable = new NewsletterEmail($sequence, $step);
        return $mailable->render();
    }
}
