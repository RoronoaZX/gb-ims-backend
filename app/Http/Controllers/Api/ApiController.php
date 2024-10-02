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
            'employee_id' => 'required|exists:employees,id',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role' => 'required|string|max:255',
         ]);

         if ($validateUser->fails()) {
            return response()->json([
                'status'=> false,
                'message'=> 'validation error',
                'errors'=> $validateUser->errors()
            ], 422);
         }

         $user = User::create([
           'employee_id' => $request->employee_id,
           'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
         ]);

        //  $branchEmployee = BranchEmployee::create([
        //     'branch_id' => $request->branch_id,
        //     'user_id' => $user->id,
        //     'time_shift' => date('H:i:s', strtotime( $request->time_shift))
        //  ]);

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
        $userData = User::where('id',auth()->user()->id)->with('employee.branchEmployee')->first();
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

    public function updateUser(Request $request, $userId)
{
    try {
        // Validate the incoming request
        $validateUser = Validator::make($request->all(), [
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
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateUser->errors()
            ], 422);
        }

        // Find the user or return a 404 response if not found
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Update the User model fields
        $user->name = $request->name;
        $user->address = $request->address;
        $user->birthdate = $request->birthdate;
        $user->sex = $request->sex;
        $user->status = $request->status;
        $user->phone = $request->phone;
        $user->role = $request->role;
        $user->save();

        // Find the associated BranchEmployee
        $branchEmployee = BranchEmployee::where('user_id', $userId)->first();
        if (!$branchEmployee) {
            return response()->json([
                'status' => false,
                'message' => 'BranchEmployee not found for the specified user'
            ], 404);
        }

        // Update the BranchEmployee model fields
        $branchEmployee->branch_id = $request->branch_id;
        $branchEmployee->time_shift = date('H:i:s', strtotime($request->time_shift));
        $branchEmployee->save();

        // Return a successful response
        return response()->json([
            'status' => true,
            'message' => 'User profile and branch employee details updated successfully',
            'data' => $user
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
        ], 500);
    }
}
}
