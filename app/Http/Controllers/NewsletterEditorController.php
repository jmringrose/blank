<?php

namespace App\Http\Controllers;

use App\Models\NewsletterStep;
use App\Services\EmailTemplateService;
use App\Services\InputSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterEditorController extends Controller
{
    protected EmailTemplateService $templateService;
    protected InputSanitizer $sanitizer;

    public function __construct(EmailTemplateService $templateService, InputSanitizer $sanitizer)
    {
        $this->templateService = $templateService;
        $this->sanitizer = $sanitizer;
    }
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
        return $this->templateService->extractEmailContent($filename, 'newsletter');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer'
        ]);

        // Use validated data as-is to preserve TinyMCE content
        $sanitized = $validated;

        $filename = Str::slug($sanitized['title']) . '.blade.php';

        $step = NewsletterStep::create([
            'title' => $sanitized['title'],
            'filename' => $filename,
            'order' => $sanitized['order'],
            'draft' => true
        ]);

        $this->saveEmailTemplate($filename, $sanitized['content'], $sanitized['title']);

        return redirect()->route('newsletter-editor.index')->with('success', 'Newsletter created successfully');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        // Use validated data as-is to preserve TinyMCE content
        $sanitized = $validated;

        $step = NewsletterStep::findOrFail($id);
        $step->update(['title' => $sanitized['title']]);

        $this->saveEmailTemplate($step->filename, $sanitized['content'], $sanitized['title']);

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
        // Pass dummy variables for template generation
        $variables = [
            'unsubscribeUrl' => '{{ $unsubscribeUrl }}',
            'record' => (object)['unsub_token' => '{{ $record->unsub_token }}']
        ];
        
        $templateView = $this->templateService->generateTemplate($content, $title, 'newsletter', $variables);
        $template = $templateView->render();
        
        if (!$this->templateService->saveTemplate($filename, $template, 'newsletter')) {
            throw new \RuntimeException('Failed to save email template');
        }
    }

}
