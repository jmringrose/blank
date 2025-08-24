<?php

namespace App\Http\Controllers;

use App\Models\QuestionStep;
use Illuminate\Http\Request;

class QuestionStepController extends Controller
{
    public function index()
    {
        return view('question-steps.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'draft' => 'boolean'
        ]);

        // Auto-assign next order number
        $validated['order'] = QuestionStep::max('order') + 1;
        
        // Auto-generate filename
        $validated['filename'] = 'question' . $validated['order'] . '.blade.php';

        $step = QuestionStep::create($validated);
        
        // Create the blade file
        $filePath = resource_path('views/emails/questions/' . $validated['filename']);
        if (!file_exists($filePath)) {
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $content = "@extends('emails.layouts.question')\n\n@section('content')\n    <h2>{{ \$validated['title'] }}</h2>\n    <p>Question content goes here...</p>\n@endsection";
            file_put_contents($filePath, $content);
        }

        return response()->json([
            'message' => 'Question step created successfully',
            'step' => $step
        ]);
    }

    public function update(Request $request, $id)
    {
        $step = QuestionStep::findOrFail($id);
        
        $validated = $request->validate([
            'order' => 'required|integer',
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'draft' => 'boolean'
        ]);

        $step->update($validated);

        return response()->json([
            'message' => 'Question step updated successfully',
            'step' => $step
        ]);
    }

    public function destroy($id)
    {
        $step = QuestionStep::findOrFail($id);
        $step->delete();

        return response()->json([
            'message' => 'Question step deleted successfully'
        ]);
    }

    public function toggle($id)
    {
        $step = QuestionStep::findOrFail($id);
        $step->draft = !$step->draft;
        $step->save();

        return response()->json([
            'message' => 'Question step status updated',
            'step' => $step
        ]);
    }

    public function data()
    {
        return QuestionStep::orderBy('order')->get();
    }
}