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
}
