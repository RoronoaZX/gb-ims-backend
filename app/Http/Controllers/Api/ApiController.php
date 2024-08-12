<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BranchEmployee;
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
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'sex' => 'required|string|in:Male,Female',
            'status' => 'required|string|in:Current,Former',
            'phone' => 'required|string|max:25',
            'role' => 'required|string|max:255',
            'branch_id' => 'required|integer',
            'time_shift' => 'required|date_format:h:i A',

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
            'name' => $request->name,
            'address' => $request->address,
            'birthdate' => $request->birthdate,
            'sex' => $request->sex,
            'status' => $request->status,
            'phone' => $request->phone,
            'role' => $request->role,
         ]);

         $branchEmployee = BranchEmployee::create([
            'branch_id' => $request->branch_id,
            'user_id' => $user->id,
            'time_shift' => date('H:i:s', strtotime( $request->time_shift))
         ]);

         return response()->json([
            // 'status' => true,
            'message' => 'User created successfully',
            'data' => $user,
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
                    'message' => 'Incorrect email & password'
                ], 500);
             }
             $user = User::where('email', $request->email)->first();
             $role = $user->role;
             return response()->json([
                'status' => true,
                'message' => 'User login successfully',
                'token' => $user->createToken('API TOKEN')->plainTextToken,
                'role' => $role
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
        // $user = auth()->user();
        $userData = User::where('id',auth()->user()->id)->with('branchEmployee')->first();
        // $userData = $user->load('branchEmployee');
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

    public function refreshToken(Request $request)
    {
        try {
            $user = Auth::user();
            $user->tokens()->delete(); // Revoke old tokens
            $newToken = $user->createToken('API TOKEN')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Token refreshed successfully',
                'token' => $newToken,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
