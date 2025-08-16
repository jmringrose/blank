<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\AdminSequenceNotification;
use App\Models\EmailSequence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class APIFormController extends Controller
{

    //=====================================================================================================
    public function formdata()
    {
        $posts = DB::connection('wordpress')
           ->table('wp_frmt_form_entry_meta')
           ->orderByDesc('date_created')
           ->limit(500)   // safety amount
           ->get();

        // Group by entry_id to create form objects
        $forms = [];
        foreach ($posts as $row) {
            $entryId = $row->entry_id;
            if (!isset($forms[$entryId])) {
                $forms[$entryId] = [
                    'entry_id' => $entryId,
                    'date_created' => $row->date_created,
                    'fields' => []
                ];
            }
            $forms[$entryId]['fields'][$row->meta_key] = $row->meta_value;
        }

        return response()->json(array_values($forms));
    }
    //=====================================================================================================
    public function formCount()
    {
        $count = DB::connection('wordpress')
            ->table('wp_frmt_form_entry_meta')
            ->distinct('entry_id')
            ->count('entry_id');

        return response()->json(['total' => $count]);
    }

    public function userFormsSummary()
    {
        $entries = DB::connection('wordpress')
            ->table('wp_frmt_form_entry_meta')
            ->select('entry_id', 'meta_value')
            ->where('meta_key', 'name-1')
            ->get();

        $userForms = [];
        foreach ($entries as $entry) {
            $name = $entry->meta_value;
            if (!isset($userForms[$name])) {
                $userForms[$name] = [];
            }
            $userForms[$name][] = $entry->entry_id;
        }

        $summary = [];
        foreach ($userForms as $name => $entryIds) {
            $summary[] = [
                'name' => $name,
                'forms_completed' => count($entryIds),
                'entry_ids' => $entryIds
            ];
        }

        return response()->json($summary);
    }
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
    //=====================================================================================================
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
    //=====================================================================================================
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
    //=====================================================================================================
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
    public function dashboardSummary()
    {
        // Get total count
        $total = \App\Models\EmailSequence::count();

        // Count per current_step
        $byStep = \App\Models\EmailSequence::select('current_step', \DB::raw('count(*) as count'))
            ->groupBy('current_step')
            ->orderBy('current_step')
            ->get();

        // Optionally, transform to a key=>value array for easier frontend use
        $steps = [];
        foreach ($byStep as $row) {
            $steps[$row->current_step] = $row->count;
        }

        return response()->json([
            'total' => $total,
            'steps' => $steps
        ]);
    }
    public function queueStatus()
    {
        $last = cache('queue_worker_heartbeat');
        $isRunning = false;
        if ($last && \Carbon\Carbon::parse($last)->gt(now()->subSeconds(90))) {
            $isRunning = true;
        }
        return response()->json(['running' => $isRunning, 'last_seen' => $last]);
    }

    public function queueHealth()
    {
        $threshold = (int) env('QUEUE_HEARTBEAT_THRESHOLD', 180);
        $ts = cache()->get('queue:heartbeat:global');

        $running = false;
        $age = null;

        if ($ts) {
            $last = Carbon::parse($ts)->utc();
            $now = now()->utc();
            $age = $now->diffInSeconds($last, true);
            $running = $age <= $threshold;
        }

        return response()->json([
            'running' => $running,
            'last_seen' => $ts,
            'age_sec' => $age,
            'cache_store' => config('cache.default'),
            'checked_at' => now()->toIso8601String(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
          ->header('Pragma', 'no-cache');
    }
}
