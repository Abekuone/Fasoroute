<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|string|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
                'message' => 'Renseignez correctement les données'
            ]);
        }

        $user = User::create([
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'birth_date' => $request->input('birth_date'),
            'gender' => $request->input('gender'),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json([
            'status' => 201,
            'success' => 'Inscription réussie',
            'user' => $user,
            'token' => $token,
        ]);
    }
}
