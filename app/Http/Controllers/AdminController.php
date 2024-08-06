<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    public function show()
    {
        $admins = Admin::all();
        return response()->json(['admins' => $admins]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'user_id' => 'required|string|max:255'
        ]);

        // Check if the user_id already exists
        $existingAdmin = Admin::where('user_id', $validated['user_id'])->first();

        if ($existingAdmin) {
            // Return a response with a custom error message
            return response()->json([
                'message' => 'Sorry! This user is already an admin',
            ], 409); // HTTP status code 409: Conflict
        }

        // Create a new admin with the validated data
        $admin = Admin::create($validated);

        // Return a response, typically JSON
        return response()->json([
            'message' => 'Admin created successfully',
            'admin' => $admin,
        ], 201); // HTTP status code 201: Created
    }
}
