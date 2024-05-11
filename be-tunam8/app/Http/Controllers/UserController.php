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
        $merged = array_merge(['user_id' => $request->user()->id, 'tags' => $request->tags]);
        $validator = Validator::make(array_merge($merged), [
            'tags' => 'required',
            'user_id' => 'required|integer|unique:user_personals,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        UserPersonal::create([
            'user_id' => $request->user()->id,
            'tags' => $request->tags,
        ]);

        $userPersonal = $request->user()->personal;

        $userPersonal->tags = $userPersonal->tags;

        return response()->json(['tags' => $userPersonal->tags, 'message' => 'User personalization created'], 201);
    }


    public function getUserPersonal(Request $request)
    {
        $validator = Validator::make(['user_id' => $request->user()->id], [
            'user_id' => 'required|integer|exists:user_personals,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $personal = $request->user()->personal;
        return response()->json($personal, 200);
    }

    public function updateUserPersonal(Request $request)
    {
        $mergedReqs = array_merge(['tags' => $request->tags, 'user_id' => $request->user()->id]);

        $validator = Validator::make($mergedReqs, [
            'tags' => 'required',
            'user_id' => 'required|integer|exists:user_personals,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $userPersonal = $request->user()->personal;
        $userPersonal->update([
            'tags' => $request->tags,
        ]);

        return response()->json(['tags' => $userPersonal->tags, 'message' => 'User personalization updated'], 200);
    }

    public function deleteUser(Request $request)
    {
        $user = User::find($request->id);
        if ($user === null) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted'], 200);
    }

    public function updateUserDetail(Request $request)
    {
        User::find($request->user()->id)->update($request->all());
        return response()->json(['message' => 'User updated'], 200);
    }
}
