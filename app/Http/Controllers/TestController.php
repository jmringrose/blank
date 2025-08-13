<?php
namespace App\Http\Controllers;
use App\Models\EmailSequence;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

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
    //==================================================================================================
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
    public function emailTest($id): View
    {
        $record = EmailSequence::find($id);
        return view('emails.steps.email_one', ['record' => $record]);
    }
    //=====================================================================================================
    public function testApi(): View
    {
        return view('utility.test_api');
    }
    //=====================================================================================================

    public function wordpress()
    {
        try {
            // Test connection first
            DB::connection('wordpress')->getPdo();
            
            $posts = DB::connection('wordpress')
                ->table('wp_frmt_form_entry_meta')
                ->select(['id', 'entry_id', 'meta_key', 'meta_value']) // Remove created_at if column doesn't exist
                ->orderByDesc('id')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $posts,
                'count' => $posts->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('WordPress connection failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'WordPress database connection failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Check WordPress database configuration'
            ], 500);
        }
    }
    //=====================================================================================================





}
