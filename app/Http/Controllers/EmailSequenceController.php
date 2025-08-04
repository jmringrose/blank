<?php

namespace App\Http\Controllers;

use App\Models\EmailSequence;
use Illuminate\Http\Request;

class EmailSequenceController extends Controller
{
    public function edit($id)
    {
        return view('email-sequences.edit');
    }

    public function show($id)
    {
        $emailSequence = EmailSequence::findOrFail($id);
        return response()->json($emailSequence);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first' => 'required|string|max:255',
            'last' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'current_step' => 'required|integer|min:0',
            'unsub_token' => 'nullable|string|max:255',
            'next_send_at' => 'nullable|date',
        ]);

        $emailSequence = EmailSequence::findOrFail($id);
        $emailSequence->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Email sequence updated successfully!',
            'data' => $emailSequence
        ]);
    }
}
