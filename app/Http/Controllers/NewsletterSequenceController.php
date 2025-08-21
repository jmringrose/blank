<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsletterSequenceController extends Controller
{
    public function index()
    {
        $sequences = NewsletterSequence::orderBy('created_at', 'desc')->get();
        return view('newsletter-sequences.index', compact('sequences'));
    }
    
    public function show($id)
    {
        $sequence = NewsletterSequence::findOrFail($id);
        return response()->json($sequence);
    }
    
    public function edit($id)
    {
        $sequence = NewsletterSequence::findOrFail($id);
        return view('newsletter-sequences.edit', compact('sequence'));
    }
    
    public function update(Request $request, $id)
    {
        $sequence = NewsletterSequence::findOrFail($id);
        
        // Direct assignment
        $sequence->first = $request->first;
        $sequence->last = $request->last;
        $sequence->email = $request->email;
        $sequence->current_step = $request->current_step;
        $sequence->next_send_at = $request->next_send_at;
        $sequence->tour_date = $request->tour_date;
        $sequence->unsub_token = $request->unsub_token;
        
        if ($request->tour_date) {
            $sequence->tour_date_str = Carbon::parse($request->tour_date)->format('j M Y');
        }
        
        $sequence->save();
        
        return response()->json([
            'success' => true,
            'message' => 'UPDATE METHOD WAS CALLED - Newsletter sequence updated successfully!',
            'data' => $sequence->fresh()
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'first' => 'required|string|max:255',
            'last' => 'required|string|max:255',
            'email' => 'required|email|unique:newsletter_sequences,email',
            'tour_date' => 'nullable|date',
            'current_step' => 'nullable|integer'
        ]);

        $sequence = NewsletterSequence::create([
            'first' => $request->first,
            'last' => $request->last,
            'email' => $request->email,
            'current_step' => $request->current_step ?? 1,
            'next_send_at' => Carbon::now()->addMinutes(5), // Start in 5 minutes
            'unsub_token' => Str::random(32),
            'tour_date' => $request->tour_date,
            'tour_date_str' => $request->tour_date ? Carbon::parse($request->tour_date)->format('j M y') : null
        ]);

        return response()->json([
            'message' => 'User added to newsletter successfully',
            'sequence' => $sequence
        ]);
    }
}