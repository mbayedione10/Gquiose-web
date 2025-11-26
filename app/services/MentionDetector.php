<?php

namespace App\Services;

use App\Models\Utilisateur;
use Illuminate\Support\Collection;

class MentionDetector
{
    /**
     * Extract all @username mentions from text
     * Returns collection of Utilisateur objects
     */
    public function extractMentions(string $text): Collection
    {
        // Extract all @mentions using regex
        preg_match_all('/@([a-zA-Z0-9_]+)/', $text, $matches);

        if (empty($matches[1])) {
            return collect();
        }

        // Get unique prenoms
        $prenoms = array_unique($matches[1]);

        // Find users by prenom
        return $this->findUsersByPrenom($prenoms);
    }

    /**
     * Find users by prenom (case-insensitive)
     */
    protected function findUsersByPrenom(array $prenoms): Collection
    {
        return Utilisateur::whereIn('prenom', $prenoms)
            ->orWhere(function ($query) use ($prenoms) {
                foreach ($prenoms as $prenom) {
                    $query->orWhereRaw('LOWER(prenom) = ?', [strtolower($prenom)]);
                }
            })
            ->get();
    }
}
