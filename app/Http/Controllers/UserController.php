<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;



class UserController extends Controller
{
    /**
     * Get all users (exclude client role - role_id 4)
     * Only Super Admin can access
     */
    public function index(Request $request)
    {
        try {
            // Check if user is Super Admin (role_id = 1)
            $currentUser = Auth::user();
            if ($currentUser->role_id != 2) {
                return response()->json([
                    'message' => 'Unauthorized. Only Super Admins can manage users.'
                ], 403);
            }

            // Get all users except clients (role_id != 4)
            $users = User::with(['client', 'role'])
                ->whereHas('role', function ($query) {
                    $query->whereIn('id', [1, 2, 4]); // Super Admin, Admin, Staff
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Format response
            $formattedUsers = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->client ? $user->client->firstname . ' ' . $user->client->surname : 'N/A',
                    'email' => $user->email,
                    'phone' => $user->phone_number,
                    'role' => $user->role_id,
                    'role_name' => $user->user_role?->name,
                    'status' => $user->status,
                    'department' => $user->client ? $user->client->client_extra?->department : null,
                    'created_at' => $user->created_at,
                    'client_id' => $user->client_id,
                ];
            });

            return response()->json([
                'users' => $formattedUsers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single user details
     * Only Super Admin can access
     */
    public function show($id)
    {
        try {
            $currentUser = Auth::user();
            if ($currentUser->role_id != 2) {
                return response()->json([
                    'message' => 'Unauthorized. Only Super Admins can view user details.'
                ], 403);
            }

            $user = User::with(['client', 'role'])
                ->whereHas('role', function ($query) {
                    $query->whereIn('id', [1, 2, 4]);
                })
                ->findOrFail($id);

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->client ? $user->client->firstname . ' ' . $user->client->surname : 'N/A',
                    'email' => $user->email,
                    'phone' => $user->phone_number,
                    'role' => $user->role_id,
                    'role_name' => $user->role ? $user->role->name : 'N/A',
                    'status' => $user->status,
                    'department' => $user->client ? $user->client->client_extra?->department : null,
                    'created_at' => $user->created_at,
                    'client_id' => $user->client_id,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create new user (Super Admin, Admin, or Staff only)
     * Only Super Admin can access
     */

    /**
     * Generate a secure random password
     */
    private function generateSecurePassword($length = 12)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*';
        
        $password = '';
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        $password .= $special[rand(0, strlen($special) - 1)];
        
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }
        
        return str_shuffle($password);
    }

    /**
     * Send welcome email with login credentials
     */
    private function sendWelcomeEmail($user, $plainPassword, $name)
    {
        try {
            $data = [
                'name' => $name,
                'email' => $user->email,
                'password' => $plainPassword,
                // 'login_url' => config('app.frontend_url') . '/login', // or your actual login URL
                'login_url' => "https://app.ilearnafricaedu.com"
            ];

            Mail::send('emails.user-welcome', $data, function($message) use ($user, $name) {
                $message->to($user->email, $name)
                    ->subject('Welcome to ' . config('app.name') . ' - Your Account Details');
            });

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
            return false;
        }
    }

    public function store(Request $request)
    {
        try {
            // Check if user is Super Admin
            $currentUser = Auth::user();
            if ($currentUser->role_id != 2) {
                return response()->json([
                    'message' => 'Unauthorized. Only Super Admins can create users.'
                ], 403);
            }

            // Validate request (removed password validation)
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|int|in:1,2,4', // Only Super Admin, Admin, Staff
                'phone' => 'nullable|string|max:20',
                'department' => 'nullable|string|max:255',
                 'status' => 'nullable|string|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Split name into parts
                $nameParts = explode(' ', trim($request->name));
                $firstname = $nameParts[0] ?? '';
                $surname = $nameParts[1] ?? '';
                $othernames = isset($nameParts[2]) ? implode(' ', array_slice($nameParts, 2)) : null;

                // Generate unique client_id
                $client_id = 'USR' . strtoupper(substr(uniqid(), -8));

                // Generate secure password
                $plainPassword = $this->generateSecurePassword(12);

                // Create client record
                $client = Client::create([
                    'client_id' => $client_id,
                    'firstname' => $firstname,
                    'surname' => $surname,
                    'othernames' => $othernames,
                    'status' => $request->status == 1 ? 'active' : 'inactive',
                    // 'created_by' => $currentUser->id,
                ]);

                // Create client_extra if department is provided
                if ($request->department) {
                    DB::table('client_extras')->insert([
                        'client_id' => $client_id,
                        'department' => $request->department,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Create user record
                $user = User::create([
                    'email' => $request->email,
                    'phone_number' => $request->phone,
                    'password' => Hash::make($plainPassword),
                    'client_id' => $client_id,
                    'role_id' => $request->role,
                ]);

                // Send welcome email with credentials
                $emailSent = $this->sendWelcomeEmail($user, $plainPassword, $firstname . ' ' . $surname);

                DB::commit();

                return response()->json([
                    'message' => $emailSent 
                        ? 'User created successfully. Login credentials have been sent to their email.' 
                        : 'User created successfully, but failed to send email notification.',
                    'user' => [
                        'id' => $user->id,
                        'name' => $firstname . ' ' . $surname,
                        'email' => $user->email,
                        'role' => $user->role_id,
                        'client_id' => $client_id,
                    ],
                    'email_sent' => $emailSent
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update user
     * Only Super Admin can access
     */
    public function update(Request $request, $id)
    {
        try {
            // Check if user is Super Admin
            $currentUser = Auth::user();
            if ($currentUser->role_id != 2) {
                return response()->json([
                    'message' => 'Unauthorized. Only Super Admins can update users.'
                ], 403);
            }

            // Find user
            $user = User::with('client')
                ->whereHas('role', function ($query) {
                    $query->whereIn('id', [1, 2, 4]);
                })
                ->findOrFail($id);

            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:6',
                'role' => 'sometimes|required|integer|in:1,2,3',
                'phone' => 'nullable|string|max:20',
                'department' => 'nullable|string|max:255',
                'status' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Update user table
                $userData = [
                    'email' => $request->email ?? $user->email,
                    'phone_number' => $request->phone ?? $user->phone_number,
                    'role_id' => $request->role ?? $user->role_id,
                    'status' => $request->status ?? $user->status,
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $user->update($userData);

                // Update client table if name or status changed
                if ($user->client) {
                    $clientData = [];

                    if ($request->filled('name')) {
                        $nameParts = explode(' ', trim($request->name));
                        $clientData['firstname'] = $nameParts[0] ?? '';
                        $clientData['surname'] = $nameParts[1] ?? '';
                        $clientData['othernames'] = isset($nameParts[2]) ? implode(' ', array_slice($nameParts, 2)) : null;
                    }

                    // if ($request->has('status')) {
                    //     $clientData['status'] = $request->status == 1 ? 'active' : 'inactive';
                    // }

                    if (!empty($clientData)) {
                        $user->client->update($clientData);
                    }

                    // Update department in client_extras
                    if ($request->has('department')) {
                        DB::table('clients_extra')
                            ->updateOrInsert(
                                ['client_id' => $user->client_id],
                                [
                                    'department' => $request->department,
                                    'updated_at' => now(),
                                ]
                            );
                    }
                }

                DB::commit();

                return response()->json([
                    'message' => 'User updated successfully',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->client ? $user->client->firstname . ' ' . $user->client->surname : 'N/A',
                        'email' => $user->email,
                        'role' => $user->role_id,
                    ]
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user (soft delete)
     * Only Super Admin can access
     */
    public function destroy($id)
    {
        try {
            // Check if user is Super Admin
            $currentUser = Auth::user();
            if ($currentUser->role_id != 2) {
                return response()->json([
                    'message' => 'Unauthorized. Only Super Admins can delete users.'
                ], 403);
            }

            // Find user
            $user = User::with('client')
                ->whereHas('role', function ($query) {
                    $query->whereIn('id', [1, 2, 4]);
                })
                ->findOrFail($id);

            // Prevent self-deletion
            if ($user->id === $currentUser->id) {
                return response()->json([
                    'message' => 'You cannot delete your own account'
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Soft delete client if exists
                if ($user->client) {
                    $user->client->delete();
                }

                // Soft delete user
                $user->delete();

                DB::commit();

                return response()->json([
                    'message' => 'User deleted successfully'
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     * Only Super Admin can access
     */
    public function statistics()
    {
        try {
            $currentUser = Auth::user();
            if ($currentUser->role_id != 1) {
                return response()->json([
                    'message' => 'Unauthorized. Only Super Admins can view statistics.'
                ], 403);
            }

            $stats = [
                'total_users' => User::whereHas('role', function ($query) {
                    $query->whereIn('id', [1, 2, 3]);
                })->count(),
                'super_admins' => User::where('role_id', 1)->count(),
                'admins' => User::where('role_id', 2)->count(),
                'staff' => User::where('role_id', 3)->count(),
                'active_users' => User::whereHas('role', function ($query) {
                    $query->whereIn('id', [1, 2, 3]);
                })->whereHas('client', function ($query) {
                    $query->where('status', 'active');
                })->count(),
            ];

            return response()->json($stats, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user status
     * Only Super Admin can access
     */
    public function toggleStatus($id)
    {
        try {
            $currentUser = Auth::user();
            if ($currentUser->role_id != 1) {
                return response()->json([
                    'message' => 'Unauthorized. Only Super Admins can change user status.'
                ], 403);
            }

            $user = User::with('client')->findOrFail($id);

            if (!$user->client) {
                return response()->json([
                    'message' => 'Client record not found for this user'
                ], 404);
            }

            // Toggle status
            $newStatus = $user->client->status === 'active' ? 'inactive' : 'active';
            $user->client->update(['status' => $newStatus]);

            return response()->json([
                'message' => 'User status updated successfully',
                'status' => $newStatus
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}