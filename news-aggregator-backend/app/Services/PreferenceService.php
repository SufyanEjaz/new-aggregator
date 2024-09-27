<?php

namespace App\Services;

use App\Repositories\Contracts\PreferenceRepositoryInterface;

class PreferenceService
{
    protected $preferenceRepo;

    public function __construct(PreferenceRepositoryInterface $preferenceRepo)
    {
        $this->preferenceRepo = $preferenceRepo;
    }

    public function getUserPreferences(int $userId)
    {
        try {
            return $this->preferenceRepo->getByUserId($userId);
            
        } catch (\Throwable $e) {
            throw new \Exception('Error fetching user preferences');
        }
    }

    public function updateUserPreferences(int $userId, array $data)
    {
        return $this->preferenceRepo->updateByUserId($userId, $data);
    }

    public function getAllPreferences()
    {
        return $this->preferenceRepo->getAll();
    }
}