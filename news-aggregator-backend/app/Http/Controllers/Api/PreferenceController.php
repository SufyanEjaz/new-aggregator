<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreferenceRequest;
use App\Services\PreferenceService;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    protected $preferenceService;

    public function __construct(PreferenceService $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    public function show()
    {
        try {
            $userId = Auth::id();
            $preferences = $this->preferenceService->getUserPreferences($userId);

            return response()->json(['message' => 'User preferences listed successfully', 'preferences' => $preferences]);

        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to fetch preferences', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(PreferenceRequest $request)
    {
        try {
            $userId = Auth::id();
            $data = [
                'source_ids' => $request->input('sources') ?? [],
                'category_ids' => $request->input('categories') ?? [],
                'authors' => $request->input('authors') ?? []
            ];
           
            $preferences = $this->preferenceService->updateUserPreferences($userId, $data);

            return response()->json(['message' => 'User preferences updated successfully', 'preferences' => $preferences]);

        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to update preferences', 'message' => $e->getMessage()], 500);
        }
    }

    public function getPreferences()
    {
        try{
            $preferences = $this->preferenceService->getAllPreferences();

            return response()->json(['message' => 'Preferences listed successfully', 'preferences' => $preferences]);
        
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to fetch preferences', 'message' => $e->getMessage()], 500);
        }
    }
}
