<?php

namespace App\Repositories\Contracts;

interface PreferenceRepositoryInterface
{
    public function getByUserId(int $userId);
    public function updateByUserId(int $userId, array $data);
    public function getAll();
}