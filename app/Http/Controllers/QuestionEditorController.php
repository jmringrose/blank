<?php

namespace App\Http\Controllers;

use App\Models\QuestionStep;
use Illuminate\Http\Request;

class QuestionEditorController extends Controller
{
    public function create()
    {
        return view('question-editor.edit');
    }

    public function edit($id)
    {
        $step = QuestionStep::findOrFail($id);
        $currentContent = $this->getEmailContent($step->filename);
        return view('question-editor.edit', compact('step', 'currentContent'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer'
        ]);

        $filename = 'question' . $request->order . '.blade.php';
        
        $step = QuestionStep::create([
            'title' => $request->title,
            'filename' => $filename,
            'order' => $request->order,
            'draft' => true
        ]);

        $this->saveEmailTemplate($filename, $request->content, $request->title);

        return redirect()->route('question-steps.index')->with('success', 'Question email created successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $step = QuestionStep::findOrFail($id);
        $step->update(['title' => $request->title]);

        // Ensure filename exists
        if (!$step->filename) {
            $step->filename = 'question' . $step->order . '.blade.php';
            $step->save();
        }

        // Decode HTML entities before saving
        $content = html_entity_decode($request->content, ENT_QUOTES, 'UTF-8');
        
        // Convert placeholder format back to PHP variables
        $content = preg_replace('/VAR_(firstName|lastName|email|unsubscribeUrl)_VAR/', '{{ $$1 }}', $content);
        $this->saveEmailTemplate($step->filename, $content, $request->title);

        if ($request->input('action') === 'save_continue') {
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Question email updated successfully']);
            }
            return redirect()->back()->with('success', 'Question email updated successfully');
        }

        return redirect()->route('question-steps.index')->with('success', 'Question email updated successfully');
    }

    public function toggle($id)
    {
        $step = QuestionStep::findOrFail($id);
        $step->draft = !$step->draft;
        $step->save();
        
        $status = $step->draft ? 'draft' : 'published';
        return redirect()->back()->with('success', "Question email set as {$status}");
    }

    public function destroy($id)
    {
        $step = QuestionStep::findOrFail($id);
        
        // Delete the file
        $filePath = resource_path('views/emails/questions/' . $step->filename);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $step->delete();
        
        return redirect()->route('question-steps.index')->with('success', 'Question email deleted successfully');
    }

    private function getEmailContent($filename)
    {
        if (!$filename) {
            return '';
        }
        
        $filePath = resource_path('views/emails/questions/' . $filename);
        
        if (!file_exists($filePath) || is_dir($filePath)) {
            return '';
        }
        
        $content = file_get_contents($filePath);
        if ($content === false) {
            return '';
        }
        
        // Remove wrapper content
        $content = preg_replace('/.*<div style=\"max-width: 600px[^>]*>/s', '', $content);
        $content = preg_replace('/<hr style=\"margin: 30px 0.*$/s', '', $content);
        
        // Convert PHP variables to placeholder format for editor
        $content = preg_replace('/\{\{\s*\$(firstName|lastName|email|unsubscribeUrl)\s*\}\}/', 'VAR_$1_VAR', $content);
        
        return trim($content);
    }

    private function saveEmailTemplate($filename, $content, $title)
    {
        if (!$filename) {
            throw new \Exception('Filename is required');
        }
        
        if (strpos($content, '<!DOCTYPE') !== false || strpos($content, '<html') !== false) {
            $template = $content;
        } else {
            $hasUnsubscribe = strpos($content, '$unsubscribeUrl') !== false || strpos($content, 'unsubscribe') !== false;
            
            $template = view('email-templates.wrapper', [
                'title' => $title,
                'emailContent' => $content,
                'hasUnsubscribe' => $hasUnsubscribe,
                'unsubscribeUrl' => '{{ $unsubscribeUrl }}'
            ])->render();
        }

        $filePath = resource_path('views/emails/questions/' . $filename);
        
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents($filePath, $template);
    }
}