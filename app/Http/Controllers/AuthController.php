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
use App\Models\Educationaldetails;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
            'firstname' => 'string',
             'surname' => 'string',
              'othernames' => 'string|nullable'
            // 'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the client record
        $client = Client::create([
            'client_id' => $randomString,
            'firstname' => $request->firstname,
            'surname' => $request->surname,
            'othernames' => $request->othernames,
        ]);

        // Create the user record
        $user = User::create([
            // 'firstname' => $request->firstname,
            // 'surname' => $request->surname,
            // 'othernames' => $request->othernames,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($auto_password),
            'client_id' => $randomString,
        ]);

        // Create the educational details record
        Educationaldetails::create([
            'client_id' => $randomString,
        ]);

        // Assign the role to the user
        $userRole = User::where('client_id', $randomString)->first();
        if ($userRole) {
            $userRole->assignRole('client');
        }

        // Send the welcome email
        Mail::to($user->email)->send(new WelcomeEmail($user, $auto_password));

        // Create and return the API token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user, // Include user details if needed
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
    
        // Return the response with the token and user role
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'role' => $user->role->name, // Assuming the user has a role relationship
            'client' => $user->client, 
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



    public function getClientId(Request $request)
    {
        // Retrieve the authenticated user
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $client = $user->client;
        // Return the client_id
        return response()->json(['status' => $client->status, 'client_id' => $user->client_id, 'email' => $user->email, 'id' => $user->id, 'firstname' => $client->firstname, 'surname' => $client->surname, 'othernames' => $client->othernames]);
    }


    public function getRole()
{
    $role = auth()->user()->roles->pluck('name')->first();


    return response()->json($role);
}
}
