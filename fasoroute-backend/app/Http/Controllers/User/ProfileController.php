<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return response()->json([
            'success' => 'Mon profile',
            'status' => 200,
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|string|unique:users,phone_number,' . $user->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        }

        $user->update([
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'birth_date' => $request->input('birth_date'),
            'gender' => $request->input('gender'),
        ]);

        return response()->json([
            'status' => 200,
            'success' => 'Profil mis à jour avec succès',
            'user' => $user,
        ]);
    }

    public function closeAccount(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        }

        if (Hash::check($request->input('password'), $user->password)) {
            $user->delete();

            return response()->json([
                'status' => 200,
                'success' => 'Compte désactivé avec succès',
            ]);
        }

        return response()->json([
            'status' => 401,
            'error' => 'Mot de passe incorrect',
            'message' => 'Le mot de passe fourni est incorrect',
        ]);
    }
}