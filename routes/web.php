<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SequenceController;
use App\Http\Controllers\FormDataController;
use App\Http\Controllers\HomeController;

// public pages - unsubscribe routes
Route::get('/unsubscribe/marketing/{token}', [\App\Http\Controllers\MarketingUnsubscribeController::class, 'unsubscribe']);
Route::get('/unsubscribe/newsletter/{token}', [\App\Http\Controllers\NewsletterUnsubscribeController::class, 'unsubscribe']);
Route::get('/unsubscribe/question/{token}', [\App\Http\Controllers\QuestionUnsubscribeController::class, 'unsubscribe'])->name('unsubscribe');


// email previews (auth required)
Route::middleware(['auth', 'verified'])->group(function () {



});

// auth pages
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', [HomeController::class, 'dashboard']);
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // user stuff
    Route::post('/theme', [ProfileController::class, 'theme'])->name('profile.theme');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // the two data tables
    Route::get('/sequences', [SequenceController::class, 'index'])->name('sequences');
    Route::get('/formdata', [FormDataController::class, 'index'])->name('formdata');

    // display and or edit the marketing emails
    Route::get('/email-sequence/{id}/edit', [SequenceController::class, 'edit'])->name('email-sequences.edit');

    // newsletter sequences
    Route::get('/newsletter-sequences', [\App\Http\Controllers\NewsletterSequenceController::class, 'index'])->name('newsletter-sequences.index');
    Route::post('/newsletter-sequences/create', [\App\Http\Controllers\NewsletterSequenceController::class, 'store'])->name('newsletter-sequences.store');
    Route::get('/newsletter-sequence/{id}/edit', [\App\Http\Controllers\NewsletterSequenceController::class, 'edit'])->name('newsletter-sequences.edit');
    Route::put('/newsletter-sequence/{id}', [\App\Http\Controllers\NewsletterSequenceController::class, 'update'])->name('newsletter-sequences.update');

    // newsletter steps
    Route::get('/newsletter-steps/data', [\App\Http\Controllers\NewsletterStepController::class, 'data']);
    Route::get('/newsletter-steps', [\App\Http\Controllers\NewsletterStepController::class, 'index'])->name('newsletter-steps.index');
    Route::post('/newsletter-steps', [\App\Http\Controllers\NewsletterStepController::class, 'store'])->name('newsletter-steps.store');
    Route::put('/newsletter-steps/{id}', [\App\Http\Controllers\NewsletterStepController::class, 'update'])->name('newsletter-steps.update');
    Route::delete('/newsletter-steps/{id}', [\App\Http\Controllers\NewsletterStepController::class, 'destroy'])->name('newsletter-steps.destroy');

    // marketing steps
    Route::get('/marketing-steps', [\App\Http\Controllers\MarketingStepController::class, 'index'])->name('marketing-steps.index');
    Route::post('/marketing-steps', [\App\Http\Controllers\MarketingStepController::class, 'store'])->name('marketing-steps.store');
    Route::get('/marketing-steps/toggle/{id}', [\App\Http\Controllers\MarketingStepController::class, 'toggle'])->name('marketing-steps.toggle');
    Route::put('/marketing-steps/{id}', [\App\Http\Controllers\MarketingStepController::class, 'update'])->name('marketing-steps.update');
    Route::delete('/marketing-steps/{id}', [\App\Http\Controllers\MarketingStepController::class, 'destroy'])->name('marketing-steps.destroy');

    // Sequence routes
    Route::get('/getsequence/{id}', [\App\Http\Controllers\API\APISequenceController::class, 'getSequence']);
    Route::put('/updatesequence/{id}', [\App\Http\Controllers\API\APISequenceController::class, 'updateSequence']);
    Route::delete('/sequence/{id}', [\App\Http\Controllers\API\APISequenceController::class, 'destroy']);
    Route::post('/sequence/bulk-delete', [\App\Http\Controllers\API\APISequenceController::class, 'bulkDelete']);

    // API routes for dashboard
    Route::get('/marketing-steps/data', function() {
        return App\Models\MarketingStep::orderBy('order')->get();
    });
    Route::get('/sequences/data', function() {
        return App\Models\EmailSequence::all();
    });
    Route::get('/newsletter-sequences/data', function() {
        return App\Models\NewsletterSequence::all();
    });

    // Test email routes
    Route::post('/send-test-email', [\App\Http\Controllers\API\APISequenceController::class, 'sendTestEmail']);
    Route::post('/send-simple-test-email', [\App\Http\Controllers\API\APISequenceController::class, 'sendSimpleTestEmail']);
    Route::post('/send-test-all-emails', [\App\Http\Controllers\API\APISequenceController::class, 'sendTestAllEmails']);

    // Email logs
    // Forms routes (moved from API to avoid auth)
    Route::get('/forms/data', [\App\Http\Controllers\API\APIFormController::class, 'formdata']);
    Route::get('/forms/count', [\App\Http\Controllers\API\APIFormController::class, 'formCount']);
    Route::get('/forms/user-summary', [\App\Http\Controllers\API\APIFormController::class, 'userFormsSummary']);

    // Queue health route (moved from API to avoid auth)
    Route::get('/health/queue', [\App\Http\Controllers\API\APISequenceController::class, 'queueHealth']);
    Route::get('/dashboard/summary', [\App\Http\Controllers\API\APISequenceController::class, 'dashboardSummary']);
    
    // Question steps data route
    Route::get('/question-steps/data', [\App\Http\Controllers\QuestionStepController::class, 'data']);
    
    // Marketing preview route
    Route::get('/preview/marketing/{step}', function($step) {
        $marketingStep = \App\Models\MarketingStep::where('order', $step)->first();
        
        if (!$marketingStep) {
            abort(404, 'Marketing step not found');
        }
        
        // Create sample marketing sequence for preview
        $sequence = (object) [
            'id' => 1,
            'first' => 'Sample',
            'last' => 'User',
            'email' => 'sample@example.com',
            'current_step' => $step,
            'name' => 'Sample User'
        ];
        
        $viewName = 'emails.marketing.' . str_replace('.blade.php', '', $marketingStep->filename);
        $unsubscribeUrl = url('/unsubscribe/marketing/sample-token');
        
        return view($viewName, [
            'sequence' => $sequence,
            'record' => $sequence,
            'firstName' => $sequence->first,
            'lastName' => $sequence->last,
            'email' => $sequence->email,
            'currentStep' => $sequence->current_step,
            'unsubscribeUrl' => $unsubscribeUrl
        ]);
    });
    
    // Newsletter preview route
    Route::get('/preview/newsletter/{step}', function($step) {
        $newsletterStep = \App\Models\NewsletterStep::where('order', $step)->first();
        
        if (!$newsletterStep) {
            abort(404, 'Newsletter step not found');
        }
        
        // Create sample newsletter sequence for preview
        $sequence = (object) [
            'id' => 1,
            'first' => 'Sample',
            'last' => 'User',
            'email' => 'sample@example.com',
            'current_step' => $step,
            'tour_date' => '2026-02-01',
            'tour_date_str' => '1 Feb 2026',
            'name' => 'Sample User'
        ];
        
        $viewName = 'emails.newsletters.' . str_replace('.blade.php', '', $newsletterStep->filename);
        $unsubscribeUrl = url('/unsubscribe/newsletter/sample-token');
        
        // Calculate days to go (sample)
        $daysToGo = 45;
        
        return view($viewName, [
            'record' => $sequence,
            'firstName' => $sequence->first,
            'lastName' => $sequence->last,
            'email' => $sequence->email,
            'currentStep' => $sequence->current_step,
            'unsubscribeUrl' => $unsubscribeUrl,
            'daysToGo' => $daysToGo
        ]);
    });
    
    // Question preview route (allows draft previews)
    Route::get('/preview/question/{step}', function($step) {
        $questionStep = \App\Models\QuestionStep::where('order', $step)->first();
        
        if (!$questionStep) {
            abort(404, 'Question step not found');
        }
        
        // Create a sample questioner for preview
        $sequence = (object) [
            'id' => 1,
            'first' => 'Sample',
            'last' => 'User',
            'email' => 'sample@example.com'
        ];
        
        if (!$questionStep->filename) {
            abort(404, 'Question template file not configured');
        }
        
        $viewName = 'emails.questions.' . str_replace('.blade.php', '', $questionStep->filename);
        $unsubscribeUrl = url('/unsubscribe/question/sample-token');
        
        return view($viewName, [
            'record' => $sequence,
            'firstName' => $sequence->first,
            'lastName' => $sequence->last,
            'email' => $sequence->email,
            'name' => trim($sequence->first . ' ' . $sequence->last),
            'unsubscribeUrl' => $unsubscribeUrl
        ]);
    });

    Route::get('/email-logs', function() {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return response()->json([]);
        }

        // Use tail to get last 1000 lines efficiently
        $output = shell_exec("tail -1000 {$logFile}");
        if (!$output) {
            return response()->json([]);
        }

        $lines = explode("\n", $output);
        $logs = [];

        for ($i = 0; $i < count($lines) - 2; $i++) {
            $line1 = $lines[$i] ?? '';
            $line2 = $lines[$i + 1] ?? '';
            $line3 = $lines[$i + 2] ?? '';

            // Look for the 3-line pattern: Building -> Sequence data -> Built successfully
            if (strpos($line1, 'Building newsletter email') !== false &&
                strpos($line2, 'Sequence data:') !== false &&
                strpos($line3, 'Newsletter email built successfully') !== false) {

                preg_match('/\[(.*?)\]/', $line3, $timeMatch);
                preg_match('/"first":"(.*?)"/', $line2, $firstMatch);
                preg_match('/"last":"(.*?)"/', $line2, $lastMatch);
                preg_match('/"email":"(.*?)"/', $line2, $emailMatch);
                preg_match('/"current_step":(\d+)/', $line2, $stepMatch);

                if ($timeMatch && $firstMatch && $emailMatch) {
                    $logs[] = [
                        'time' => $timeMatch[1],
                        'who' => trim(($firstMatch[1] ?? '') . ' ' . ($lastMatch[1] ?? '')),
                        'email' => $emailMatch[1] ?? '',
                        'what' => 'Newsletter Step ' . ($stepMatch[1] ?? '?'),
                        'when' => \Carbon\Carbon::parse($timeMatch[1])->diffForHumans()
                    ];
                }
            }
        }

        // Look for newsletter, marketing, and question email send logs
        foreach ($lines as $line) {
            if (strpos($line, 'Newsletter email sent') !== false || strpos($line, 'Marketing email sent') !== false || strpos($line, 'Question email sent') !== false) {
                preg_match('/\[(.*?)\]/', $line, $timeMatch);
                preg_match('/"recipient_name":"(.*?)"/', $line, $nameMatch);
                preg_match('/"recipient_email":"(.*?)"/', $line, $emailMatch);
                preg_match('/"step_number":(\d+)/', $line, $stepMatch);
                preg_match('/"step_title":"(.*?)"/', $line, $titleMatch);

                $type = strpos($line, 'Newsletter') !== false ? 'Newsletter' : (strpos($line, 'Question') !== false ? 'Question' : 'Marketing');

                if ($timeMatch && $nameMatch && $emailMatch && $stepMatch && $titleMatch) {
                    $logs[] = [
                        'time' => $timeMatch[1],
                        'who' => $nameMatch[1],
                        'email' => $emailMatch[1],
                        'what' => $type . ' Step ' . $stepMatch[1] . ': ' . $titleMatch[1],
                        'when' => \Carbon\Carbon::parse($timeMatch[1])->diffForHumans()
                    ];
                }
            }
        }

        // Sort by time and return last 10
        usort($logs, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return response()->json(array_slice($logs, 0, 10)); // Last 10 emails
    });

    // Image upload routes
    Route::post('/upload-image', [\App\Http\Controllers\ImageController::class, 'upload']);
    Route::get('/images', [\App\Http\Controllers\ImageController::class, 'list']);

    // Newsletter sequences
    Route::get('/newsletter-sequence/{id}', [\App\Http\Controllers\NewsletterSequenceController::class, 'show']);
    Route::post('/newsletter-sequences', [\App\Http\Controllers\NewsletterSequenceController::class, 'store']);





    // storage images API
    // API endpoint for TinyMCE image browser
    Route::get('/api/images', function() {
        try {
            // Check if img directory exists
            $imgPath = public_path('img/public_images');

            if (!is_dir($imgPath)) {
                // Try to create the directory
                if (!mkdir($imgPath, 0755, true)) {
                    return response()->json([
                        'error' => 'img directory does not exist',
                        'path' => $imgPath,
                        'images' => []
                    ]);
                }
            }

            // Get all image files from public/img directory
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
            $images = [];

            $files = scandir($imgPath);
            if ($files === false) {
                return response()->json([
                    'error' => 'Cannot read img directory',
                    'path' => $imgPath,
                    'images' => []
                ]);
            }

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;

                $filePath = $imgPath . '/' . $file;
                if (!is_file($filePath)) continue;

                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowedExtensions)) continue;

                $images[] = [
                    'name' => $file,
                    'path' => '/img/public_images/' . $file,
                    'url' => asset('img/public_images/' . $file),
                    'size' => filesize($filePath),
                    'modified' => date('Y-m-d H:i:s', filemtime($filePath))
                ];
            }

            // Sort by name
            usort($images, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            return response()->json([
                'success' => true,
                'path' => $imgPath,
                'count' => count($images),
                'images' => $images
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unable to load images',
                'message' => $e->getMessage(),
                'images' => []
            ], 500);
        }
    })->name('api.images');

    Route::get('/storage-images', function() {
        $images = [];
        $storagePath = storage_path('app/public/images');

        if (is_dir($storagePath)) {
            $files = glob($storagePath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
            foreach ($files as $file) {
                $filename = basename($file);
                $images[] = [
                    'name' => $filename,
                    'url' => '/storage/images/' . $filename
                ];
            }
        }

        return response()->json($images);
    });

    // newsletter editor
    Route::get('/newsletter-editor/create', [\App\Http\Controllers\NewsletterEditorController::class, 'create'])->name('newsletter-editor.create');
    Route::get('/newsletter-editor/{id}/edit', [\App\Http\Controllers\NewsletterEditorController::class, 'edit'])->name('newsletter-editor.edit');
    Route::post('/newsletter-editor', [\App\Http\Controllers\NewsletterEditorController::class, 'store'])->name('newsletter-editor.store');
    Route::put('/newsletter-editor/{id}', [\App\Http\Controllers\NewsletterEditorController::class, 'update'])->name('newsletter-editor.update');
    Route::get('/newsletter-editor/toggle/{id}', [\App\Http\Controllers\NewsletterEditorController::class, 'toggle'])->name('newsletter-editor.toggle');
    Route::delete('/newsletter-editor/{id}', [\App\Http\Controllers\NewsletterEditorController::class, 'destroy'])->name('newsletter-editor.destroy');

    // marketing editor
    Route::get('/marketing-editor/create', [\App\Http\Controllers\MarketingEditorController::class, 'create'])->name('marketing-editor.create');
    Route::get('/marketing-editor/toggle/{id}', [\App\Http\Controllers\MarketingEditorController::class, 'toggle'])->name('marketing-editor.toggle');
    Route::get('/marketing-editor/{id}/edit', [\App\Http\Controllers\MarketingEditorController::class, 'edit'])->name('marketing-editor.edit');
    Route::post('/marketing-editor', [\App\Http\Controllers\MarketingEditorController::class, 'store'])->name('marketing-editor.store');
    Route::put('/marketing-editor/{id}', [\App\Http\Controllers\MarketingEditorController::class, 'update'])->name('marketing-editor.update');
    Route::delete('/marketing-editor/{id}', [\App\Http\Controllers\MarketingEditorController::class, 'destroy'])->name('marketing-editor.destroy');

    // question sequences
    Route::get('/question-sequences', [\App\Http\Controllers\QuestionSequenceController::class, 'index'])->name('question-sequences.index');
    Route::post('/question-sequences', [\App\Http\Controllers\QuestionSequenceController::class, 'store'])->name('question-sequences.store');
    Route::post('/question-sequences/send', [\App\Http\Controllers\QuestionSequenceController::class, 'send']);
    Route::get('/question-sequence/{id}', [\App\Http\Controllers\QuestionSequenceController::class, 'show']);
    Route::put('/question-sequence/{id}', [\App\Http\Controllers\QuestionSequenceController::class, 'update']);
    Route::delete('/question-sequence/{id}', [\App\Http\Controllers\QuestionSequenceController::class, 'destroy']);
    Route::post('/question-sequence/bulk-delete', [\App\Http\Controllers\QuestionSequenceController::class, 'bulkDelete']);
    Route::get('/question-sequences/data', [\App\Http\Controllers\QuestionSequenceController::class, 'data']);

    // question steps
    Route::get('/question-steps', [\App\Http\Controllers\QuestionStepController::class, 'index'])->name('question-steps.index');
    Route::post('/question-steps', [\App\Http\Controllers\QuestionStepController::class, 'store'])->name('question-steps.store');
    Route::put('/question-steps/{id}', [\App\Http\Controllers\QuestionStepController::class, 'update']);
    Route::delete('/question-steps/{id}', [\App\Http\Controllers\QuestionStepController::class, 'destroy']);
    Route::get('/question-steps/toggle/{id}', [\App\Http\Controllers\QuestionStepController::class, 'toggle']);
    Route::get('/question-steps/data', [\App\Http\Controllers\QuestionStepController::class, 'data']);

    // question editor
    Route::get('/question-editor/create', [\App\Http\Controllers\QuestionEditorController::class, 'create'])->name('question-editor.create');
    Route::get('/question-editor/{id}/edit', [\App\Http\Controllers\QuestionEditorController::class, 'edit'])->name('question-editor.edit');
    Route::post('/question-editor', [\App\Http\Controllers\QuestionEditorController::class, 'store'])->name('question-editor.store');
    Route::put('/question-editor/{id}', [\App\Http\Controllers\QuestionEditorController::class, 'update'])->name('question-editor.update');
    Route::get('/question-editor/toggle/{id}', [\App\Http\Controllers\QuestionEditorController::class, 'toggle'])->name('question-editor.toggle');
    Route::delete('/question-editor/{id}', [\App\Http\Controllers\QuestionEditorController::class, 'destroy'])->name('question-editor.destroy');



});

// WordPress route
Route::get('/wordpress', function () {
    return view('wordpress');
});

require __DIR__.'/auth.php';
