<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::paginate(10);
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
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|max:15',
            'profile' => 'nullable|in:admin,employee,user',
            'is_approved' => 'required|boolean',
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->code = Str::uuid();
        $user->password = bcrypt($request->password);
        $user->profile = $request->profile;
        $user->is_approved = $request->is_approved;

        $user->save();

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::withTrashed()->find($id);

        if (empty($user)) {
            return response()->json(['error' => 'User Not Found'], 404);
        }

        return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        return $request->user();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->find($id);

        if (empty($user)) {
            return response()->json(['error' => 'User Not Found'], 404);
        }

        $action = $request->query('action');

        if (!$action) {

            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users,email,' . $id,
                'profile' => 'nullable|in:admin,employee,user',
                'is_approved' => 'required|boolean',
            ]);

            $user->email = $request->email;
            $user->name = $request->name;
            $user->profile = $request->profile;
            $user->is_approved = $request->is_approved;

            $user->save();

            return response()->json($user, 201);
        }

        switch ($action) {
            case 'approved':
                $user->is_approved = true;
                break;

            case 'disapproved':
                $user->is_approved = false;
                break;

            case 'restore':
                $user->restore();
                break;
        }

        $user->save();

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (empty($user)) {
            return response()->json(['error' => 'User Not Found'], 404);
        }

        $user->delete();

        return response()->json($user);
    }
}
