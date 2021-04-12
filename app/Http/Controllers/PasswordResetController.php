<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($token)
    {
        $password_reset = PasswordReset::where('token', $token)->first();

        if (empty($password_reset)) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        if ($password_reset->verified) {
            return response()->json(['error' => 'Token already used'], 409);
        }

        $expies = strtotime($password_reset->expires_in) < time() ? true : false;

        if ($expies) {
            return response()->json(['error' => 'Token expired'], 409);
        }

        return response()->json($password_reset);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required_with:password_confirmation|min:8|max:15',
            'token' => 'required|exists:password_resets,token'
        ]);

        $user = User::where('email', $request->email)->first();

        $password_reset = PasswordReset::where('token', $request->token)->latest('created_at')->first();

        if (empty($password_reset)) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        if ($password_reset->verified) {
            return response()->json(['error' => 'Token already used'], 409);
        }

        $expies = strtotime($password_reset->expires_in) < time() ? true : false;

        if ($expies) {
            return response()->json(['error' => 'Token expired'], 409);
        }

        $password_reset->verified = true;
        $password_reset->save();

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'user' => $user,
            'password_reset' => $password_reset,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
