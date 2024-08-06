<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class TokenValidationController extends Controller
{
    /**
     * Validate the token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    // use Laravel\Sanctum\PersonalAccessToken;

    public function validateToken(Request $request)
    {
        $token = $request->bearerToken();
        
        if (is_null($token)) {
            return response()->json([
                'success' => false,
                'message' => 'No token provided.'
            ], 401);
        }
    
        $hashedToken = hash('sha256', $token);
    
        $tokenExists = PersonalAccessToken::where('token', $hashedToken)->exists();
    
        if ($tokenExists) {
            return response()->json([
                'success' => true,
                'message' => 'Token is valid.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token.'
            ], 401);
        }
    }
    
}
