<?php
namespace App\Http\Controllers;
use App\Models\EmailSequence;
use Illuminate\Http\Request;
use Illuminate\View\View;
class TestController extends Controller
{
    //=====================================================================================================
    public function history()
    {
        return EmailSequence::all()->toJson();
    }
    //=====================================================================================================
    public function thirdTest(Request $request): View
    {
        return view('utility.test_datatable', ['user' => $request->user(),]);
    }
    //=====================================================================================================
    public function test(Request $request): View
    {
        return view('utility.test', ['user' => $request->user(),]);
    }
    //=====================================================================================================
    public function secondTest(Request $request): View
    {
        return view('utility.test_video', ['user' => $request->user(),]);
    }
    //=====================================================================================================
    public function fourthTest(Request $request): View
    {
        return view('utility.test_form', ['user' => $request->user(),]);
    }
    //=====================================================================================================
    public function emailTest(Request $request): View
    {
        $record = EmailSequence::find(83);
        return view('emails.steps.email_one', ['record' => $record]);
    }
    //=====================================================================================================
}
