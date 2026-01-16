<?php

namespace App\Controllers\Api;

use App\Models\User;

class UserController {
    public function index() {
        $users = User::all();
        return json_response($users);
    }

    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);

        $validator = new \Framework\Security\Validator($data, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->validate()) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => \Framework\Security\Hash::make($data['password']),
            ]);

            return json_response(['user' => $user], 201);
        }

        return json_response(['errors' => $validator->errors()], 422);
    }

    public function show($id) {
        $user = User::find($id);

        if (!$user) {
            return json_response(['error' => 'User not found'], 404);
        }

        return json_response(['user' => $user]);
    }

    public function update($id) {
        $user = User::find($id);

        if (!$user) {
            return json_response(['error' => 'User not found'], 404);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $validator = new \Framework\Security\Validator($data, [
            'name' => 'required|min:3',
            'email' => 'required|email',
        ]);

        if ($validator->validate()) {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            return json_response(['user' => $user]);
        }

        return json_response(['errors' => $validator->errors()], 422);
    }

    public function destroy($id) {
        $user = User::find($id);

        if (!$user) {
            return json_response(['error' => 'User not found'], 404);
        }

        $user->delete();

        return json_response(['message' => 'User deleted successfully']);
    }
}
