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

        // Auto-assign next order number (same as ID)
        $nextOrder = QuestionStep::max('order') + 1;
        $validated['order'] = $nextOrder;
        
        // Auto-generate filename as questionX.blade.php
        $validated['filename'] = 'question' . $nextOrder . '.blade.php';

        $step = QuestionStep::create($validated);
        
        // Create the blade file
        $filePath = resource_path('views/emails/questions/' . $validated['filename']);
        if (!file_exists($filePath)) {
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $content = "<!DOCTYPE html>\n<html>\n<head>\n    <meta charset=\"utf-8\">\n    <title>{$validated['title']}</title>\n</head>\n<body style=\"font-family: Arial, sans-serif; line-height: 1.6; color: #333;\">\n    <div style=\"max-width: 600px; margin: 0 auto; padding: 20px;\">\n        <h1 style=\"color: #2c3e50; font-size: 24px; margin-bottom: 20px;\">{$validated['title']}</h1>\n        <p style=\"font-size: 16px; line-height: 1.6; color: #333;\">Hi {{ \$firstName }},</p>\n        <p style=\"font-size: 16px; line-height: 1.6; color: #333;\">Your question content goes here...</p>\n        <p style=\"font-size: 16px; line-height: 1.6; color: #333;\">Best regards,<br />The Real Cool Photo Tours Team</p>\n    </div>\n</body>\n</html>";
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
            'order' => 'required|integer|unique:question_steps,order,' . $id,
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'draft' => 'boolean'
        ]);
        
        // Update filename if order changed
        if ($step->order != $validated['order']) {
            $oldFilename = $step->filename;
            $validated['filename'] = 'question' . $validated['order'] . '.blade.php';
            
            // Rename the file
            $oldPath = resource_path('views/emails/questions/' . $oldFilename);
            $newPath = resource_path('views/emails/questions/' . $validated['filename']);
            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
            }
        }

        $step->update($validated);

        return response()->json([
            'message' => 'Question step updated successfully',
            'step' => $step
        ]);
    }

    public function destroy($id)
    {
        $step = QuestionStep::findOrFail($id);
        
        // Delete the blade file
        if ($step->filename) {
            $filePath = resource_path('views/emails/questions/' . $step->filename);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
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