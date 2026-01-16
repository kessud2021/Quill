<?php

namespace Database\Seeds;

class UserSeeder {
    public function run() {
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => \Framework\Security\Hash::make('password123'),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => \Framework\Security\Hash::make('password123'),
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'password' => \Framework\Security\Hash::make('password123'),
            ],
        ];

        foreach ($users as $user) {
            \App\Models\User::create($user);
        }
    }
}
