<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
     public function changePassword(Request $request)
    {
        //hash the current password and compare it with the password in the database
        if(!Hash::check($request->current_password, Auth::user()->password)){
            return response()->json([
                'status' => 'failed',
                'message' => 'Current password is incorrect',
            ], 401);
        }
        if(strcmp($request->current_password, $request->password) == 0){
            return response()->json([
                'status' => 'failed',
                'message' => 'New password cannot be same as your current password',
            ], 401);
        }
        if($request->password != $request->password_confirmation){
            return response()->json([
                'status' => 'failed',
                'message' => 'New password and confirm password must be same',
            ], 401);
        }
        $request->user()->password = $request->password;
        $request->user()->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully',
        ], 200);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user(),
        ], 200);
    }
   
}