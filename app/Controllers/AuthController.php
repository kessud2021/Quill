<?php

namespace App\Controllers;

use App\Models\User;
use Framework\Security\Hash;

class AuthController {
    public function login() {
        return view('auth.login');
    }

    public function postLogin() {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (auth()->attempt($email, $password)) {
            return redirect('/');
        }

        return redirect('/login')->with('error', 'Invalid credentials');
    }

    public function register() {
        return view('auth.register');
    }

    public function postRegister() {
        $data = $_POST;

        $validator = new \Framework\Security\Validator($data, [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!$validator->validate()) {
            return redirect('/register')->with('errors', $validator->errors());
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        auth()->login($user);

        return redirect('/');
    }

    public function logout() {
        auth()->logout();
        return redirect('/');
    }
}
