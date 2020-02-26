<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    public function index()
    {
        $users = User::all();

        return $this->showAll($users);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return $this->showOne($user);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ];

        $this->validate($request, $rules);

        $fields = $request->all();
        $fields['password'] = bcrypt($request->password);

        $user = User::create($fields);

        return $this->showOne($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed'
        ];

        $this->validate($request, $rules);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if (!$user->isDirty()) {
            return $this->errorResponse('At least one value must be specified to update this record', 422);
        } else {
            $user->save();
            return $this->showOne($user);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return $this->showOne($user);
    }
}
