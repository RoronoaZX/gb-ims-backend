<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return response()->json($users);
    }

    public function searchUser(Request $request)
    {
        $keyword = $request->input('keyword');

        $request->validate([
            'keyword' => 'required|string|max:255'
        ]);

        if (empty($keyword)) {
            // If the keyword is an empty string, fetch all users
            $results = User::orderBy('created_at', 'desc')->get();
        } else {
            // Otherwise, perform the search
            $results = User::search($keyword)->get();
        }

        return response()->json($results);

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'user_fullname' => 'required|string|max:255',
            'user_address' => 'required|string|max:255',
            'user_birthdate' => 'required|date',
            'user_sex' => 'required|string|in:Male,Female',
            'user_status' => 'required|string|in:Current,Former',
            'user_phone_number' => 'required|string|max:25',
            'user_position' => 'required|string|max:255',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->user_fullname,
            'address' => $request->user_address,
            'birthdate' => $request->user_birthdate,
            'sex' => $request->user_sex,
            'status' => $request->user_status,
            'phone' => $request->user_phone_number,
            'role' => $request->user_position,
            'remember_token' => Str::random(60),
        ]);
        return response()->json($user);


    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
