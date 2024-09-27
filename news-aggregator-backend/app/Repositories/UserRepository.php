<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function createUser(array $data)
    {
        return User::create([
            'name' => $data['username'] ?? $data['email'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function getUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function createToken($user)
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function deleteTokens($user)
    {
        return $user->tokens()->delete();
    }
}
