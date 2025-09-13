<?php

namespace App\Http\Controllers;

use App\Models\MarketingStep;
use App\Models\EmailSequence;
use App\Models\NewsletterSequence;

class DataController extends Controller
{
    public function marketingSteps()
    {
        return MarketingStep::orderBy('order')->get();
    }

    public function sequences()
    {
        return EmailSequence::all();
    }

    public function newsletterSequences()
    {
        return NewsletterSequence::all();
    }
}