<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSequence;
use Illuminate\Http\Request;

class NewsletterSequenceController extends Controller
{
    public function index()
    {
        return view('newsletter-sequences.index');
    }

    public function edit($id)
    {
        $sequence = NewsletterSequence::findOrFail($id);
        return view('newsletter-sequences.edit', compact('sequence'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first' => 'required|string|max:255',
            'last' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'current_step' => 'required|integer|min:0',
            'next_send_at' => 'nullable|date',
        ]);

        $sequence = NewsletterSequence::findOrFail($id);
        $sequence->update($validated);

        return redirect()->route('newsletter-sequences.index')->with('success', 'Newsletter sequence updated successfully!');
    }
}