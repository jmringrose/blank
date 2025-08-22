<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSequence;
use Illuminate\Http\Request;

class NewsletterUnsubscribeController extends Controller
{
    public function unsubscribe($token)
    {
        $sequence = NewsletterSequence::where('unsub_token', $token)->first();

        if (!$sequence) {
            return view('generic-unsubscribe.invalid-token');
        }

        $sequence->current_step = 0;
        $sequence->save();

        return view('generic-unsubscribe.unsubscribed', [
            'sequence' => $sequence,
            'firstName' => $sequence->first,
            'lastName' => $sequence->last,
            'fullName' => $sequence->first . ' ' . $sequence->last
        ]);
    }
}
