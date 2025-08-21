<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSequence;
use Illuminate\Http\Request;

class APINewsletterSequenceController extends Controller
{
    public function sequencedata()
    {
        return NewsletterSequence::all()->toJson();
    }

    public function getSequence($id)
    {
        $sequence = NewsletterSequence::findOrFail($id);
        return response()->json($sequence);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first' => 'required|string|max:255',
            'last' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'current_step' => 'required|integer|min:0',
            'next_send_at' => 'nullable|date',
            'tour_date' => 'nullable|date',
            'tour_date_str' => 'nullable|string|max:255',
            'unsub_token' => 'nullable|string|max:255',
        ]);

        $sequence = NewsletterSequence::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Newsletter sequence created successfully!',
            'data' => $sequence
        ]);
    }

    public function updateSequence(Request $request, $id)
    {
        $validated = $request->validate([
            'first' => 'required|string|max:255',
            'last' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'current_step' => 'required|integer|min:0',
            'next_send_at' => 'nullable|date',
            'tour_date' => 'nullable|date',
            'tour_date_str' => 'nullable|string|max:255',
            'unsub_token' => 'nullable|string|max:255',
        ]);

        $sequence = NewsletterSequence::findOrFail($id);
        $sequence->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Newsletter sequence updated successfully!',
            'data' => $sequence
        ]);
    }

    public function destroy($id)
    {
        $sequence = NewsletterSequence::findOrFail($id);
        $sequence->delete();

        return response()->json([
            'message' => 'Newsletter sequence deleted successfully'
        ], 200);
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $deletedCount = NewsletterSequence::whereIn('id', $validated['ids'])->delete();
        return response()->json([
            'message' => $deletedCount . ' newsletter sequences deleted successfully'
        ], 200);
    }

    public function summary()
    {
        $sequences = NewsletterSequence::select('first', 'last', 'current_step')
            ->orderBy('first')
            ->get();

        $summary = $sequences->map(function ($sequence) {
            return [
                'name' => trim($sequence->first . ' ' . $sequence->last),
                'current_step' => $sequence->current_step
            ];
        });

        return response()->json($summary);
    }
}