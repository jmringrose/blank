<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmailSequence;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminSequenceNotification;
use Carbon\Carbon;

class SequenceController extends Controller
{
    /**
     * Add a new sequence entry or update an existing one.
     */
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
        Mail::to(env('ADMIN_EMAIL'))->send(new AdminSequenceNotification('subscribed', $sequence));

        // Return a success response
        return response()->json([
            'message' => 'Sequence added successfully',
            'sequence' => $sequence,
        ], 200);
    }

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
        \Mail::to(env('ADMIN_EMAIL'))->send(new \App\Mail\AdminSequenceNotification('unsubscribed', $sequence));

        return view('email-sequences.unsubscribed');
    }

    /**
     * Reset the sequence based on the given sequence ID.
     */
    public function resetSequence(Request $request)
    {
        // Validate the incoming POST data
        $validated = $request->validate([
            'sequence_id' => 'required|integer|exists:email_sequences,id', // Ensure sequence exists
        ]);

        // Find the sequence record
        $sequence = EmailSequence::find($validated['sequence_id']);

        // Reset the sequence attributes
        $sequence->current_step = 0;
        $sequence->next_send_at = null;
        $sequence->save();

        // Return a success response
        return response()->json([
            'message' => 'Sequence reset successfully',
            'sequence' => $sequence,
        ], 200);
    }

    /**
     * Delete a single sequence.
     */
    public function destroy($id)
    {
        $sequence = EmailSequence::findOrFail($id);
        $sequence->delete();

        return response()->json([
            'message' => 'Sequence deleted successfully'
        ], 200);
    }

    /**
     * Delete multiple sequences.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $deletedCount = EmailSequence::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'message' => $deletedCount . ' sequences deleted successfully'
        ], 200);
    }
}
