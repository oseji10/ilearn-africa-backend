<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Client;
class AuthController extends Controller
{

    public function register(Request $request)
    {
        $randomString = strtoupper(Str::random(10));
        $auto_password = strtoupper(Str::random(7));
        try {
            $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
                'phone_number' => 'required|string|max:11',
                // 'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($auto_password),
                'client_id' => $randomString,
            ]);

            $client = Client::create([
                
                'client_id' => $randomString,
            ]);

            // Send the welcome email
            Mail::to($user->email)->send(new WelcomeEmail($user, $auto_password));

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            // Check if the validation error is for the unique email constraint
            if ($e->validator->errors()->has('email')) {
                return response()->json([
                    'message' => 'This user has already been created',
                ], 409); // HTTP status code 409: Conflict
            }

            throw $e;
        }
    }

    // Login Method
    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Determine if the input is an email or phone number
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        // Adjust the credentials array accordingly
        $credentials = [$loginType => $request->login, 'password' => $request->password];

        // Attempt to authenticate the user
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Retrieve the authenticated user
        $user = User::where($loginType, $request->login)->firstOrFail();

        
        // Revoke all previous tokens (uncomment if needed)
        $user->tokens()->delete();


        // Generate the authentication token using Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the response with the token
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }


//     public function login(Request $request)
// {
//     $credentials = $request->only('login', 'password');

//     if (Auth::attempt($credentials)) {
//         $user = Auth::user();
//         $token = $user->createToken('Personal Access Token')->plainTextToken;

//         return response()->json([
//             'success' => true,
//             'token' => $token
//         ]);
//     }
    

    // Logout Method
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
