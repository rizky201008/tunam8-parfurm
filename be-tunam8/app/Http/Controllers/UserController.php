<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPersonal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $personal = UserPersonal::where('user_id', $request->user()->id)->first();
        if ($personal === null) {
            return response()->json(['message' => 'User personalization not found'], 404);
        }
        $personal->tags = json_decode($personal->tags);
        return response()->json(['personal' => $personal], 200);
    }

    public function updateUserPersonal(Request $request)
    {
        $mergedReqs = array_merge($request->all(), $request->user()->toArray());

        $validator = Validator::make($mergedReqs, [
            'tags' => 'required|array',
            'id' => 'required|integer|exists:user_personals,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $user = User::with('personal')->find($validated['id']);
        $user->personal->tags = json_encode($validated['tags']);
        $user->personal->save();
        
        $user->personal->tags = json_decode($user->personal->tags);

        return response()->json(['personal' => $user->personal, 'message' => 'User personalization updated'], 200);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if ($user === null) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted'], 200);
    }
}
