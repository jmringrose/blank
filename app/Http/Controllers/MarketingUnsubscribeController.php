<?php

namespace App\Http\Controllers;

use App\Models\EmailSequence;
use Illuminate\Http\Request;

class MarketingUnsubscribeController extends Controller
{
    public function unsubscribe($token)
    {
        $sequence = EmailSequence::where('unsub_token', $token)->first();

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
