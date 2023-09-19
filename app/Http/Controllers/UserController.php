<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validate user input
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Create a new user record
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate an authentication token
        $token = $user->createToken('authToken')->plainTextToken;

        // Return the user data and token
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }


    public function login(Request $request)
    {
        // Validate user input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Authentication successful; generate a new token
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            // Return the user data and token
            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 200);
        } else {
            // Authentication failed
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
}