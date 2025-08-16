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
            return response()->json([
                'message' => 'Newsletter unsubscribe failed - invalid token',
            ], 404);
        }

        $sequence->current_step = 0;
        $sequence->save();

        return view('newsletters.unsubscribed', compact('sequence'));
    }
}
