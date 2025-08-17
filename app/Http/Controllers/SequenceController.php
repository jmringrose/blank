<?php

namespace App\Http\Controllers;

use App\Models\EmailSequence;
use Illuminate\Http\Request;

class SequenceController extends Controller
{
   //=====================================================================================================
    public function index()
    {
        return view('marketing-sequences.sequence_datatable');
    }
    //=====================================================================================================
    public function edit($id)
    {
        $sequence = EmailSequence::findOrFail($id);
        return view('marketing-sequences.edit', compact('sequence'));
    }
    //=====================================================================================================
    public function show($id): \Illuminate\Http\JsonResponse
    {
        $emailSequence = EmailSequence::findOrFail($id);
        return response()->json($emailSequence);
    }

    //=====================================================================================================
    public function unsubscribe(Request $request)
    {
        $token = $request->query('token');
        
        if (!$token) {
            return response()->json(['message' => 'Token required'], 400);
        }
        
        $sequence = EmailSequence::where('unsub_token', $token)->first();
        
        if (!$sequence) {
            return response()->json(['message' => 'Invalid token'], 404);
        }
        
        // Check if already unsubscribed
        if ($sequence->current_step == 0) {
            return view('generic-unsubscribe.already-unsubscribed', [
                'sequence' => $sequence,
                'firstName' => $sequence->first,
                'lastName' => $sequence->last,
                'fullName' => $sequence->first . ' ' . $sequence->last
            ]);
        }
        
        $sequence->current_step = 0;
        $sequence->save();
        
        return view('generic-unsubscribe.unsubscribed', [
            'sequence' => $sequence,
            'firstName' => $sequence->first,
            'lastName' => $sequence->last,
            'fullName' => $sequence->first . ' ' . $sequence->last
        ]);
    }
    //=====================================================================================================
}
