<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $user = User::where('email', $request->email)->first();
            $ability = ($user->role == 'admin') ? 'admin' : 'user';
            $token = $user->createToken('access-token', [$ability]);

            return response()->json([
                'message' => 'Login success 🎉🎉🎉',
                'token' => $token->plainTextToken
            ], 200);
        }
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    public function user(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        return response()->json(['message' => 'Successfully registered'], 201);
    }
}
