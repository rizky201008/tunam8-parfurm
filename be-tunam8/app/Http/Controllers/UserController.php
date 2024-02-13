<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPersonal;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAllUsers()
    {
        return response()->json(User::all(), 200);
    }

    public function personalizeUser(Request $request)
    {
        $validated = $request->validate([
            'tags' => 'required|array',
        ]);

        $validated['tags'] = json_encode($validated['tags']);

        $personal = UserPersonal::where('user_id', $request->user()->id)->first();
        if ($personal === null) {
            $createdPersonal = UserPersonal::create([
                'user_id' => $request->user()->id,
                'tags' => $validated['tags'],
            ]);

            return response()->json(['personal' => $createdPersonal, 'message' => 'User personalization created'], 201);
        }

        return response()->json(['personal' => $personal, 'message' => 'User personalization already exists'], 200);
    }

    public function getUserPersonal(Request $request)
    {
        $user = User::find($request->user()->id);
        $personal = UserPersonal::where('user_id', $request->user()->id)->first();
        return response()->json(['personal' => $user->personal], 200);
    }

    public function updateUserPersonal(Request $request)
    {
        $validated = $request->validate([
            'tags' => 'required|array',
        ]);

        $user = User::find($request->user()->id);
        $user->personal->tags = $validated['tags'];
        $user->personal->save();

        return response()->json(['personal' => $user->personal, 'message' => 'User personalization updated'], 200);
    }
}
