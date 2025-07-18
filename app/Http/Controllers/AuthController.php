<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Mail\WelcomeEmail;
use App\Mail\PasswordEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Educationaldetails;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Password;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Str;
// use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $randomString = strtoupper(Str::random(10));
        $auto_password = strtoupper(Str::random(7));
    
        try {
            $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
                'phone_number' => [
                    'required',
                    'string',
                    'unique:users',
                    function ($attribute, $value, $fail) {
                        // Custom validation for phone number length
                        if (strlen($value) > 11) {
                            $fail('The phone number must not exceed 11 digits.');
                        }
    
                        // Custom validation for phone numbers starting with +
                        if (strpos($value, '+') === 0) {
                            $fail('The phone number must not start with "+".');
                        }
    
                        // Custom validation for Nigerian phone numbers (optional)
                        if (substr($value, 0, 4) === '234') {
                            $fail('The phone number must not start with the country code.');
                        }
                    },
                ],
                'firstname' => 'string',
                'surname' => 'string',
                'othernames' => 'string|nullable'
            ], [
                'email.unique' => 'The email address has already been taken.',
                'phone_number.unique' => 'The phone number has already been taken.',
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
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($auto_password),
                'client_id' => $randomString,
                'role_id' => 3
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
                'message' => 'Success! Your signup was successful. Please check your email for login details',
                'user' => $user,
            ]);
        } catch (ValidationException $e) {
            // Return the detailed validation error messages
            return response()->json([
                'errors' => $e->validator->errors(),
            ], 422); // HTTP status code 422: Unprocessable Entity
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
        // $user->tokens()->delete();
    
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
    



    

    // Logout Method
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }



    public function getClientId(Request $request)
    // public function getUserProfile(Request $request)
{
    // Retrieve the authenticated user
    $user = $request->user();

    // Check if the user is authenticated
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Access the related Client data
    $client = $user->client;

    // Check if the client relationship exists
    if (!$client) {
        return response()->json(['error' => 'Client not found'], 404);
    }

    // Access the related ProfileImage data
    $profileImage = $user->profileImage;

    // Check if the profile image relationship exists
    $imageUrl = $profileImage ? $profileImage->image_url : null;

    // Return the user, client, and profile image data
    return response()->json([
        'status' => $client->status,
        'client_id' => $client->client_id, 
        'email' => $user->email,
        'id' => $user->id,
        'role' => $user->role_id,
        'firstname' => $client->firstname,
        'surname' => $client->surname,
        'othernames' => $client->othernames,
        'phone_number' => $user->phone_number,
        'image_url' => $imageUrl // Add the image_url to the response
    ]);
}



    public function getRole()
{
    $role = auth()->user()->roles->pluck('name')->first();


    return response()->json($role);
}

// Forgot password
public function forgotPassword(Request $request)
{
    // Generate a random 5-digit OTP
    $pass = mt_rand(10000, 99999);
    $password = Hash::make($pass);

    // Find the user by email or phone number
    $forgot_password = User::where('email', $request->login)
        ->orWhere('phone_number', $request->login)
        ->first();

    // Check if a user was found
    if (!$forgot_password) {
        return response()->json([
            'message' => 'User not found',
        ], 404); // HTTP status code 404: Not Found
    }

    // Update the user's password
    $forgot_password->password = $password;
    $forgot_password->save();

    // Optionally, send the OTP via email or SMS here

    return response()->json([
        'message' => 'OTP sent to your email',
        'password' => $pass, // In a real application, you wouldn't return the password here
    ]);
}


// Change Password
public function changePassword(Request $request)
    {

        // Validate the request data
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
        ], [
            'new_password.min' => 'The new password must be at least 8 characters.',
        ]);
        

        // Check if the current password matches
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return response()->json([
                'message' => "Current password is incorrect",
            ]);
        }

        // Update the user's password
        $user = Auth::user()->password;
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => "Password succesfully changed!",
            'user' => $user,
        ]);
    }





    public function sendResetLinkEmail(Request $request)
{
    $request->validate(['login' => 'required']);

    // Find the user by email
    $user = DB::table('users')->where('email', $request->login)
    ->orWhere('phone_number', $request->login)
    ->first();
    if (!$user) {
        return response()->json(['message' => 'Email not found'], 404);
    }
    $email = $user->email;
    // return $email;
    // // Generate the token
    $token = Str::random(60);

    // Insert the token into the password_resets table
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $email],
        [
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]
    );

    // Create the reset link
    // $url = config('myconfig.url');
    // $url = env('URL');
    // echo getenv('URL');
    // return $url;
    $url = "https://app.ilearnafricaedu.com";
    $resetLink = ($url .'/auth/reset-password?token=' . $token . '&email=' . urlencode($email));

    // Send the reset link via email if needed
    Mail::to($email)->send(new PasswordEmail($resetLink));
    return response()->json(['message' => 'Password reset link sent to your email', 'reset_link' => $resetLink], 200);
}


public function reset(Request $request)
{
    $validator = Validator::make($request->all(), [
        'token' => 'required',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    if ($status == Password::PASSWORD_RESET) {
        return response()->json(['message' => 'Password has been reset successfully.']);
    } else {
        return response()->json(['message' => __($status)], 500);
    }
}


public function checkUser(Request $request)
    {
        try {
            $request->validate([
                'email' => 'nullable|email|max:255',
                'phonenumber' => 'nullable|string|max:15',
            ]);

            $emailExists = false;
            $phonenumberExists = false;

            if ($request->email) {
                $emailExists = User::where('email', $request->email)->exists();
            }

            if ($request->phonenumber) {
                $phonenumberExists = User::where('phone_number', $request->phonenumber)->exists();
            }

            return response()->json([
                'success' => true,
                'email_exists' => $emailExists,
                'phonenumber_exists' => $phonenumberExists,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error checking user:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to check user.',
            ], 500);
        }
    }
    
}
