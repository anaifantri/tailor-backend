<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if(!auth()->attempt($credentials)){
            return response()->json([
                'message' => 'Credentials are not valid'
            ], 401);
        }

        $user = auth()->user();
        if($user->is_active){
            return response()->json([
                'token' => $user->createToken('laravel11')->plainTextToken,
                'user' => $user
                ]);
        }else{
            return response()->json([
                'error' => 'account_inactive',
                'message' => 'Your account is currently inactive. Please contact IT support.'
            ], 403);
        }
    }
}
