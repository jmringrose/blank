<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\AdminSequenceNotification;
use App\Models\EmailSequence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class APISequencePublicController extends Controller
{

     //=====================================================================================================
    public function addToSequence(Request $request)
    {
        // Validate the incoming POST data
        $validated = $request->validate([
            'First_Name' => 'required|string', // Assuming 'first' expects a string
            'Last_Name' => 'required|string',  // Assuming 'last' expects a string
            'Email' => 'required|email', // Validate as email
        ]);

        // Create a new EmailSequence instance
        $sequence = new EmailSequence();
        $sequence->first = $validated['First_Name'];
        $sequence->last = $validated['Last_Name'];
        $sequence->email = $validated['Email'];
        $sequence->current_step = 1;
        $sequence->unsub_token = Str::random(12);
        $sequence->next_send_at = Carbon::now('America/New_York'); // Set the current time as the next send time
        $sequence->save();

        // Notify admin
        Mail::to(env('ADMIN_EMAIL'))->queue(new AdminSequenceNotification('subscribed', $sequence));

        // Return a success response
        return response()->json([
            'message' => 'Sequence added successfully',
            'sequence' => $sequence,
        ], 200);
    }
    //=====================================================================================================
    public function unsubscribe($token)
    {
        $sequence = \App\Models\EmailSequence::where('unsub_token', $token)->first();

        if (!$sequence) {
            return response()->json([
                'message' => 'Unsubscribed failed',
                'sequence' => $sequence,
            ], 404);
        }

        $sequence->current_step = 0; // Or another value that marks as unsubscribed
        $sequence->save();

        // Notify admin
        // Notify admin
        \Mail::to(env('ADMIN_EMAIL'))->queue(new \App\Mail\AdminSequenceNotification('unsubscribed', $sequence));

        return view('marketing.legacy-unsubscribed');
    }
}
