<?php

namespace App\Http\Controllers;

use App\Models\QuestionSequence;
use Illuminate\Http\Request;

class QuestionUnsubscribeController extends Controller
{
    public function unsubscribe($token)
    {
        $sequence = QuestionSequence::where('unsub_token', $token)->first();
        
        if (!$sequence || $sequence->unsubscribed) {
            return view('unsubscribe.invalid');
        }
        
        $sequence->update(['unsubscribed' => true]);
        
        return view('unsubscribe.success', [
            'type' => 'question emails',
            'email' => $sequence->email
        ]);
    }
}