<?php

namespace App\Http\Controllers;

use App\Models\EmailSequence;
use Illuminate\Http\Request;

class SequenceController extends Controller
{
   //=====================================================================================================
    public function index()
    {
        return view('email-sequences.sequence_datatable');
    }
    //=====================================================================================================
    public function edit($id)
    {
        return view('email-sequences.edit');
    }
    //=====================================================================================================
    public function show($id): \Illuminate\Http\JsonResponse
    {
        $emailSequence = EmailSequence::findOrFail($id);
        return response()->json($emailSequence);
    }

    //=====================================================================================================
}
