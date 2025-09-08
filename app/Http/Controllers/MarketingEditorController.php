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

        // Handle HTML entities carefully for complete documents
        if (strpos($request->content, '<!DOCTYPE') !== false || strpos($request->content, '<html') !== false) {
            // For complete HTML documents, minimal processing to preserve formatting
            $content = $request->content;
        } else {
            // For simple content, decode HTML entities and fix Laravel variables
            $content = html_entity_decode($request->content, ENT_QUOTES, 'UTF-8');
            $content = str_replace(['$record->name', '$record->first', '$record->last', '$record->email'], ['$name', '$firstName', '$lastName', '$email'], $content);
        }
        
        // Convert placeholder format back to PHP variables
        $content = preg_replace('/VAR_(firstName|lastName|email|currentStep|unsubscribeUrl)_VAR/', '{{ $$1 }}', $content);
        $this->saveEmailTemplate($step->filename, $content, $request->title);

        if ($request->input('action') === 'save_continue') {
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Marketing email updated successfully']);
            }
            return redirect()->back()->with('success', 'Marketing email updated successfully');
        }

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
            
            // Remove everything before and after our content - be more specific
            $content = preg_replace('/.*?<div style="max-width: 6[0-9]{2}px[^>]*>/s', '', $content);
            $content = preg_replace('/<hr style="margin: 30px 0.*$/s', '', $content);
            
            // Remove any wrapper divs at start and end
            while (preg_match('/^\s*<div style="max-width: 6[0-9]{2}px[^>]*>/', $content)) {
                $content = preg_replace('/^\s*<div style="max-width: 6[0-9]{2}px[^>]*>\s*/', '', $content);
            }
            while (preg_match('/<\/div>\s*$/', $content)) {
                $content = preg_replace('/\s*<\/div>\s*$/', '', $content);
            }
            
            // Decode HTML entities to prevent JavaScript syntax errors
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            
            // Convert PHP variables to placeholder format for editor
            $content = preg_replace('/\{\{\s*\$(firstName|lastName|email|currentStep|unsubscribeUrl)\s*\}\}/', 'VAR_$1_VAR', $content);
            
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
            // Clean up extra spans around variables (only for simple content)
            $content = $this->cleanupVariableSpans($content);
            
            // Check if unsubscribe link already exists
            $hasUnsubscribe = strpos($content, '$unsubscribeUrl') !== false || strpos($content, 'unsubscribe') !== false;
            
            $unsubscribeSection = '';
            if (!$hasUnsubscribe) {
                $unsubscribeSection = $this->getUnsubscribeFooter();
            }
            
            // Simple template wrapper for basic content
            $template = view('email-templates.wrapper', [
                'title' => $title,
                'emailContent' => $content,
                'hasUnsubscribe' => $hasUnsubscribe,
                'unsubscribeUrl' => '{{ $unsubscribeUrl }}'
            ])->render();
        }

        $filePath = resource_path('views/emails/marketing/' . $filename);
        
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents($filePath, $template);
    }
    
    private function cleanupVariableSpans($content)
    {
        // Remove multiple nested spans around Laravel variables
        $content = preg_replace('/<span[^>]*>\s*(<span[^>]*>)*\s*(\{\{[^}]+\}\})\s*(<\/span>)*\s*<\/span>/', '$2', $content);
        
        // Clean up any remaining nested spans around variables
        $content = preg_replace('/<span[^>]*>(\{\{[^}]+\}\})<\/span>/', '$1', $content);
        
        return $content;
    }
    
    private function getUnsubscribeFooter()
    {
        return config('email-templates.unsubscribe_footer');
    }
}