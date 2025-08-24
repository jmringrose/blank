<?php

namespace App\Http\Controllers;

use App\Models\QuestionSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionSequenceController extends Controller
{
    public function index()
    {
        return view('question-sequences.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first' => 'required|string|max:255',
            'last' => 'required|string|max:255',
            'email' => 'required|email|unique:question_sequences,email',
            'question_step_id' => 'required|exists:question_steps,id'
        ]);

        $validated['unsub_token'] = Str::random(32);
        $validated['sent'] = false;

        $sequence = QuestionSequence::create($validated);
        $sequence->load('questionStep');

        return response()->json([
            'message' => 'Question sequence created successfully',
            'sequence' => $sequence
        ]);
    }

    public function show($id)
    {
        $sequence = QuestionSequence::findOrFail($id);
        return response()->json($sequence);
    }

    public function update(Request $request, $id)
    {
        $sequence = QuestionSequence::findOrFail($id);
        
        $validated = $request->validate([
            'first' => 'required|string|max:255',
            'last' => 'required|string|max:255',
            'email' => 'required|email|unique:question_sequences,email,' . $id,
            'question_step_id' => 'required|exists:question_steps,id',
            'email_history' => 'nullable|array'
        ]);

        // Reset sent flag if question changed
        if ($sequence->question_step_id != $validated['question_step_id']) {
            $validated['sent'] = false;
        }

        $sequence->update($validated);
        $sequence->load('questionStep');

        return response()->json([
            'message' => 'Question sequence updated successfully',
            'sequence' => $sequence
        ]);
    }

    public function destroy($id)
    {
        $sequence = QuestionSequence::findOrFail($id);
        $sequence->delete();

        return response()->json([
            'message' => 'Question sequence deleted successfully'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $deletedCount = QuestionSequence::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'message' => $deletedCount . ' question sequences deleted successfully'
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:question_sequences,id'
        ]);

        $sequence = QuestionSequence::with('questionStep')->findOrFail($validated['id']);
        
        if (!$sequence->questionStep) {
            return response()->json(['error' => 'No question assigned'], 400);
        }

        try {
            \Log::info('Attempting to send question email', [
                'recipient' => $sequence->email,
                'question' => $sequence->questionStep->title,
                'filename' => $sequence->questionStep->filename
            ]);
            
            // Send email immediately (not queued)
            \Mail::to($sequence->email)->send(new \App\Mail\QuestionEmail($sequence, $sequence->questionStep));
            
            $sequence->sent = true;
            $emailHistory = $sequence->email_history ?? [];
            $emailHistory[] = $sequence->questionStep->title;
            $sequence->email_history = $emailHistory;
            $sequence->save();
            
            \Log::info('Question email sent successfully', [
                'recipient' => $sequence->email,
                'question' => $sequence->questionStep->title
            ]);
            
            return response()->json([
                'message' => 'Email sent successfully',
                'email_history' => $sequence->email_history
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send question email', [
                'sequence_id' => $sequence->id,
                'recipient' => $sequence->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }

    public function data()
    {
        return QuestionSequence::with('questionStep')->get();
    }
}