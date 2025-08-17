<?php

namespace App\Http\Controllers;

use App\Models\NewsletterStep;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterEditorController extends Controller
{
    public function index()
    {
        $steps = NewsletterStep::orderBy('order')->get();
        return view('newsletter-editor.index', compact('steps'));
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
        $filePath = resource_path('views/emails/newsletters/' . $filename);
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            // Extract content between the div and hr tags, handling multiline
            if (preg_match('/<div[^>]*>\s*(.+?)\s*<hr/s', $content, $matches)) {
                $extracted = trim($matches[1]);
                // Clean up whitespace but preserve structure
                $extracted = preg_replace('/\n\s+/', '\n', $extracted);
                $extracted = preg_replace('/\s+/', ' ', $extracted);
                $extracted = str_replace(['> <', '>\n<'], ['><', '><'], $extracted);
                return $extracted;
            }
        }
        return '';
    }

    public function store(Request $request)
    {
        $request->validate([
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

        $this->saveEmailTemplate($filename, $request->content, $request->title);

        return redirect()->route('newsletter-editor.index')->with('success', 'Newsletter created successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $step = NewsletterStep::findOrFail($id);
        $step->update(['title' => $request->title]);

        $this->saveEmailTemplate($step->filename, $request->content, $request->title);

        return redirect()->route('newsletter-editor.index')->with('success', 'Newsletter updated successfully');
    }

    private function saveEmailTemplate($filename, $content, $title)
    {
        $template = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($title) . '</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        ' . $content . '
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666;">
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>';

        $filePath = resource_path('views/emails/newsletters/' . $filename);
        
        // Ensure directory exists
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents($filePath, $template);
    }
}