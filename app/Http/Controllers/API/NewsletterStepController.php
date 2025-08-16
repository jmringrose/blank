<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NewsletterStep;
use Illuminate\Http\Request;

class NewsletterStepController extends Controller
{
    public function index()
    {
        return NewsletterStep::orderBy('order')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'order' => 'required|integer',
            'title' => 'required|string|max:255',
            'filename' => 'nullable|string|max:255',
            'draft' => 'boolean'
        ]);

        return NewsletterStep::create($request->all());
    }

    public function show(NewsletterStep $newsletterStep)
    {
        return $newsletterStep;
    }

    public function update(Request $request, NewsletterStep $newsletterStep)
    {
        $request->validate([
            'order' => 'integer',
            'title' => 'string|max:255',
            'filename' => 'nullable|string|max:255',
            'draft' => 'boolean'
        ]);

        $newsletterStep->update($request->all());
        return $newsletterStep;
    }

    public function destroy(NewsletterStep $newsletterStep)
    {
        $newsletterStep->delete();
        return response()->json(['message' => 'Newsletter step deleted']);
    }
}
