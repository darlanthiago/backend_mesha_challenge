<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Mockery\Generator\StringManipulation\Pass\Pass;

class AuthController extends Controller
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
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Credentials does not match'
            ], 401);
        }


        if (!$user->is_approved) {
            return response()->json([
                'error' => 'Not approved user',
            ], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken($user->code, [$user->profile])->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
            'email' => 'required|email'
        ]);

        $user = User::where('email', '=', $request->email)->first();

        if (empty($user)) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        // delete all others tokens
        PasswordReset::where('email', '=', $request->email)->delete();

        $token = Str::random(60);

        $password_reset = new PasswordReset();
        $password_reset->token = $token;
        $password_reset->email = $request->email;
        $password_reset->expires_in = now()->addHours(1);
        $password_reset->save();

        $data = [
            'link' => env('FRONT_END_URL'),
            'token' => $token,
        ];

        Mail::send(new ForgotPassword($user, $data));

        return response()->json($password_reset);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'User logout successfuly'
        ], 200);
    }
}
