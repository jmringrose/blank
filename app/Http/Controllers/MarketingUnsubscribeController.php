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
            return response()->json([
                'message' => 'Marketing unsubscribe failed - invalid token',
            ], 404);
        }

        $sequence->current_step = 0;
        $sequence->save();

        return view('generic-unsubscribe.unsubscribed', compact('sequence'));
    }
}
