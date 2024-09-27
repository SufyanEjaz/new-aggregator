<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $data)
    {
        return $this->userRepository->createUser($data);
    }

    public function authenticateUser(array $credentials)
    {
        $user = $this->userRepository->getUserByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        return $this->userRepository->createToken($user);
    }

    public function logoutUser($user)
    {
        return $this->userRepository->deleteTokens($user);
    }
}
