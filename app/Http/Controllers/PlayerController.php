<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PlayerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getKey(Request $request)
    {
        //\Log::info("x-xsrf-token=> ".$request->header('x-xsrf-token'));
        //\Log::info("session token=> ". csrf_token());
        \Log::info("session token=> ". Cache::get('key'));

        return response()->json([
            'key' => '123456789',
        ]);
    }
}
