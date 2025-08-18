<?php

namespace App\Http\Controllers;

use App\Models\MarketingStep;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketingEditorController extends Controller
{
    public function create()
    {
        return view('marketing-editor.edit');
    }

    public function edit($id)
    {
        $step = MarketingStep::findOrFail($id);
        $currentContent = $this->getEmailContent($step->filename);
        return view('marketing-editor.edit', compact('step', 'currentContent'));
    }

    public function store(Request $request)
    {
        \Log::info('Marketing store called', $request->all());
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer'
        ]);

        $filename = 'step' . $request->order . '.blade.php';
        
        $step = MarketingStep::create([
            'title' => $request->title,
            'filename' => $filename,
            'order' => $request->order,
            'draft' => true
        ]);

        \Log::info('Marketing step created', ['id' => $step->id]);

        $this->saveEmailTemplate($filename, $request->content, $request->title);

        return redirect()->route('marketing-editor.index')->with('success', 'Marketing email created successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $step = MarketingStep::findOrFail($id);
        $step->update(['title' => $request->title]);

        $this->saveEmailTemplate($step->filename, $request->content, $request->title);

        return redirect()->route('marketing-steps.index')->with('success', 'Marketing email updated successfully');
    }

    public function toggle($id)
    {
        $step = MarketingStep::findOrFail($id);
        \Log::info('Toggle called', ['id' => $id, 'current_draft' => $step->draft]);
        
        $step->draft = !$step->draft;
        $step->save();
        
        \Log::info('After toggle', ['new_draft' => $step->draft]);
        
        $status = $step->draft ? 'draft' : 'published';
        return redirect()->back()->with('success', "Marketing email set as {$status}");
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
        
        return redirect()->route('marketing-editor.index')->with('success', 'Marketing email deleted successfully');
    }

    private function getEmailContent($filename)
    {
        $filePath = resource_path('views/emails/marketing/' . $filename);
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            
            // Remove everything before and after our content
            $content = preg_replace('/.*<div style="max-width: 600px[^>]*>/s', '', $content);
            $content = preg_replace('/<hr style="margin: 30px 0.*$/s', '', $content);
            
            return trim($content);
        }
        return '';
    }

    private function saveEmailTemplate($filename, $content, $title)
    {
        // If content already contains DOCTYPE, save as-is (complex template)
        if (strpos($content, '<!DOCTYPE') !== false || strpos($content, '<html') !== false) {
            $template = $content;
        } else {
            // Check if unsubscribe link already exists
            $hasUnsubscribe = strpos($content, '$unsubscribeUrl') !== false || strpos($content, 'unsubscribe') !== false;
            
            $unsubscribeSection = '';
            if (!$hasUnsubscribe) {
                $unsubscribeSection = '
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666;">
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a>
        </p>';
            }
            
            // Simple template wrapper for basic content
            $template = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($title) . '</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        ' . $content . $unsubscribeSection . '
    </div>
</body>
</html>';
        }

        $filePath = resource_path('views/emails/marketing/' . $filename);
        
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents($filePath, $template);
    }
}