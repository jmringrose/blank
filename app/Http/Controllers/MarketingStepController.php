<?php

namespace App\Http\Controllers;

use App\Models\MarketingStep;
use Illuminate\Http\Request;

class MarketingStepController extends Controller
{
    public function index()
    {
        $steps = MarketingStep::orderBy('order')->get();
        return view('marketing-steps.index', compact('steps'));
    }

    public function store(Request $request)
    {
        $step = MarketingStep::create($request->all());
        
        // Copy file if duplicating
        if ($request->original_filename && $request->filename) {
            $originalPath = resource_path('views/emails/marketing/' . $request->original_filename);
            $newPath = resource_path('views/emails/marketing/' . $request->filename);
            
            if (file_exists($originalPath)) {
                copy($originalPath, $newPath);
            }
        }
        
        return response()->json(['message' => 'Marketing step created successfully']);
    }

    public function toggle($id)
    {
        $step = MarketingStep::findOrFail($id);
        $step->draft = !$step->draft;
        $step->save();
        
        $status = $step->draft ? 'draft' : 'published';
        return redirect()->back()->with('success', "Marketing step set as {$status}");
    }

    public function update(Request $request, $id)
    {
        $step = MarketingStep::findOrFail($id);
        $step->update($request->all());
        
        return response()->json(['message' => 'Marketing step updated successfully']);
    }

    public function destroy($id)
    {
        $step = MarketingStep::findOrFail($id);
        
        // Delete the file
        $filePath = resource_path('views/emails/marketing/' . $step->filename);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete the database record
        $step->delete();
        
        return response()->json(['message' => 'Marketing step deleted successfully']);
    }
}