<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Category;
use App\Models\Preference;
use App\Models\Source;
use App\Repositories\Contracts\PreferenceRepositoryInterface;

class PreferenceRepository implements PreferenceRepositoryInterface
{
    public function getByUserId(int $userId)
    {
        try {
            // Define default preferences
            $defaultSettings = [
                'source_ids' => [],
                'category_ids' => [],
                'authors' => []
            ];

            // Use firstOrCreate and set default settings if creating a new preference
            return Preference::firstOrCreate(
                ['user_id' => $userId],
                ['settings' => $defaultSettings]  // This will set default settings if it doesn't exist
            );

        } catch (\Throwable $e) {
            throw new \Exception('Error fetching user preferences');
        }
    }

    public function updateByUserId(int $userId, array $data)
    {
        try {
            // Fetch existing preferences or create a new one
            $preference = Preference::firstOrCreate(['user_id' => $userId]);
            
            // Merge existing settings with the new data
            $updatedSettings = array_merge(
                $preference->settings ?? [], // Existing settings (if any)
                $data // New data from the request
            );

            // Update the preference with the merged settings
            $preference->update([
                'settings' => $updatedSettings
            ]);

            return $preference;

        } catch (\Throwable $e) {
            throw new \Exception('Error updating preferences');
        }
    }

    public function getAll()
    {
        try {
            // Fetch all distinct categories
            $categories = Category::select('id', 'name', 'slug')->orderBy('name')->get();

            // Fetch all distinct sources
            $sources = Source::select('id', 'name', 'slug')->orderBy('name')->get();

            // Fetch distinct authors from the articles table
            $authors = Article::select('author')
                ->distinct()
                ->whereNotNull('author') // Make sure null authors are excluded
                ->orderBy('author')
                ->get()
                ->pluck('author'); // We only need the author field

            // Return the data in a single response
            return [
                'categories' => $categories,
                'sources' => $sources,
                'authors' => $authors,
            ];

        } catch (\Throwable $e) {
            throw new \Exception('Error fetching preferences');
        }
    }
}