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
        // Debug: log all request data
        \Log::info('Newsletter store request data:', $request->all());
        
        // Get all possible field variations
        $first = $request->first ?? $request->input('data.first') ?? $request->input('First_Name');
        $last = $request->last ?? $request->input('data.last') ?? $request->input('Last_Name');
        $email = $request->email ?? $request->input('data.email') ?? $request->input('Email');
        
        \Log::info('Extracted fields:', ['first' => $first, 'last' => $last, 'email' => $email]);
        
        // Minimal validation - just check if we have the basic fields
        if (!$first || !$last || !$email) {
            \Log::error('Missing required fields');
            return response()->json(['message' => 'Missing required fields: first, last, email', 'debug' => ['first' => $first, 'last' => $last, 'email' => $email]], 422);
        }
        
        // Check for duplicates - return success if already exists
        if (NewsletterSequence::where('email', $email)->exists()) {
            \Log::info('User already exists in newsletter: ' . $email);
            return response()->json(['message' => 'User already exists in newsletter (no action needed)'], 200);
        }

        $sequence = NewsletterSequence::create([
            'first' => $first,
            'last' => $last,
            'email' => $email,
            'current_step' => 1,
            'next_send_at' => Carbon::now()->addMinutes(5),
            'unsub_token' => Str::random(32)
        ]);

        return response()->json([
            'message' => 'User added to newsletter successfully',
            'sequence' => $sequence
        ]);
    }
}