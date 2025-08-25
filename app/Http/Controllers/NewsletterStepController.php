<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsletterStepController extends Controller
{
    public function index()
    {
        return view('newsletter-steps.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $step = \App\Models\NewsletterStep::create($request->all());
        
        // Copy file if duplicating
        if ($request->original_filename && $request->filename) {
            $originalPath = resource_path('views/emails/newsletters/' . $request->original_filename);
            $newPath = resource_path('views/emails/newsletters/' . $request->filename);
            
            \Log::info('Duplicate file copy attempt', [
                'original_filename' => $request->original_filename,
                'new_filename' => $request->filename,
                'original_path' => $originalPath,
                'new_path' => $newPath,
                'original_exists' => file_exists($originalPath)
            ]);
            
            if (file_exists($originalPath)) {
                $result = copy($originalPath, $newPath);
                \Log::info('File copy result', ['success' => $result]);
            }
        }
        
        return response()->json(['message' => 'Newsletter step created successfully']);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $step = \App\Models\NewsletterStep::findOrFail($id);
        
        $validated = $request->validate([
            'order' => 'required|integer|unique:newsletter_steps,order,' . $id,
            'title' => 'required|string|max:255',
            'filename' => 'required|string|max:255',
            'draft' => 'boolean'
        ]);
        
        // If filename changed, rename the file
        if ($step->filename !== $validated['filename']) {
            $oldPath = resource_path('views/emails/newsletters/' . $step->filename);
            $newPath = resource_path('views/emails/newsletters/' . $validated['filename']);
            
            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
            }
        }
        
        $step->update($validated);
        
        return response()->json([
            'message' => 'Newsletter step updated successfully',
            'step' => $step
        ]);
    }

    public function data()
    {
        return \App\Models\NewsletterStep::orderBy('order')->get();
    }

    public function destroy(string $id)
    {
        $step = \App\Models\NewsletterStep::findOrFail($id);
        
        // Delete the file
        $filePath = resource_path('views/emails/newsletters/' . $step->filename);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete the database record
        $step->delete();
        
        return response()->json(['message' => 'Newsletter step deleted successfully']);
    }
}
