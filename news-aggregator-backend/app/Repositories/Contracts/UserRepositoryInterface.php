<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function createUser(array $data);
    public function getUserByEmail(string $email);
    public function createToken($user);
    public function deleteTokens($user);
}
