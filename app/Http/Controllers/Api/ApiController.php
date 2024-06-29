<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ApiController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
         [
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'user_fullname' => 'required|string|max:255',
            'user_address' => 'required|string|max:255',
            'user_birthdate' => 'required|date',
            'user_sex' => 'required|string|in:Male,Female',
            'user_status' => 'required|string|in:Current,Former',
            'user_phone_number' => 'required|string|max:25',
            'user_position' => 'required|string|max:255',
         ]);

         if ($validateUser->fails()) {
            return response()->json([
                'status'=> false,
                'message'=> 'validation error',
                'errors'=> $validateUser->errors()
            ], 422);
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
         ]);

         return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'token' => $user->createToken('API TOKEN')->plainTextToken
         ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status'=> false,
                'message'=> $th->getMessage(),
            ], 500);
        }

    }

    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
            [
               'email' => 'required|email',
               'password' => 'required',

            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status'=> false,
                    'message'=> 'validation error',
                    'errors'=> $validateUser->errors()
                ], 422);
             }

             if(!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'messaage' => 'Email & password does not match our record'
                ], 500);
             }
             $user = User::where('email', $request->email)->first();
             return response()->json([
                'status' => true,
                'message' => 'User login successfully',
                'token' => $user->createToken('API TOKEN')->plainTextToken
             ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status'=> false,
                'message'=> $th->getMessage(),
            ], 500);
        }
    }

    public function profile()
    {
        $userData = auth()->user();
        return response()->json([
            'status' => true,
            'message' => 'User login successfully',
            'data' => $userData,
            'id' => auth()->user()->id
         ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'User log out',
            'data' => [],

         ], 200);
    }
}
