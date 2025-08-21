<?php

namespace App\Http\Controllers;

use App\Models\NewsletterStep;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterEditorController extends Controller
{
    public function create()
    {
        return view('newsletter-editor.edit');
    }

    public function edit($id)
    {
        $step = NewsletterStep::findOrFail($id);
        $currentContent = $this->getEmailContent($step->filename);
        return view('newsletter-editor.edit', compact('step', 'currentContent'));
    }

    private function getEmailContent($filename)
    {
        $filePath = resource_path('views/emails/newsletters/' . $filename);
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);

            // Remove everything before and after our content
            $content = preg_replace('/.*<div style="max-width: 600px[^>]*>/s', '', $content);
            $content = preg_replace('/<hr style="margin: 30px 0.*$/s', '', $content);

            return trim($content);
        }
        return '';
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer'
        ]);

        $filename = Str::slug($request->title) . '.blade.php';

        $step = NewsletterStep::create([
            'title' => $request->title,
            'filename' => $filename,
            'order' => $request->order,
            'draft' => true
        ]);

        $this->saveEmailTemplate($filename, $validated['content'], $validated['title']);

        return redirect()->route('newsletter-editor.index')->with('success', 'Newsletter created successfully');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $step = NewsletterStep::findOrFail($id);
        $step->update(['title' => $validated['title']]);

        $this->saveEmailTemplate($step->filename, $validated['content'], $validated['title']);

        if ($request->input('action') === 'save_continue') {
            return redirect()->back()->with('success', 'Newsletter updated successfully');
        }

        return redirect()->route('newsletter-steps.index')->with('success', 'Newsletter updated successfully');
    }

    public function toggle($id)
    {
        $step = NewsletterStep::findOrFail($id);
        $step->draft = !$step->draft;
        $step->save();

        $status = $step->draft ? 'draft' : 'published';
        return redirect()->back()->with('success', "Newsletter set as {$status}");
    }

    public function destroy($id)
    {
        $step = NewsletterStep::findOrFail($id);

        // Delete the file
        $filePath = resource_path('views/emails/newsletters/' . $step->filename);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the database record
        $step->delete();

        return redirect()->route('newsletter-editor.index')->with('success', 'Newsletter deleted successfully');
    }

    private function saveEmailTemplate($filename, $content, $title)
    {
        // Clean up extra spans around variables
        $content = $this->cleanupVariableSpans($content);
        
        // Check if unsubscribe link already exists
        $hasUnsubscribe = strpos($content, '$unsubscribeUrl') !== false || strpos($content, 'unsubscribe') !== false;

        $unsubscribeSection = '';
        if (!$hasUnsubscribe) {
            $unsubscribeSection = $this->getUnsubscribeFooter();
        }

        $template = view('email-templates.wrapper', [
            'title' => $title,
            'emailContent' => $content,
            'hasUnsubscribe' => $hasUnsubscribe
        ])->render();

        $filePath = resource_path('views/emails/newsletters/' . $filename);

        // Ensure directory exists
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
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
