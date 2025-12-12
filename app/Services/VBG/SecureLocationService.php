<?php

namespace App\Services\VBG;

use App\Models\Ville;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SecureLocationService
{
    // Rayon d'approximation en kilomètres (pour anonymiser)
    const ANONYMIZATION_RADIUS_KM = 0.5; // 500 mètres

    /**
     * Anonymise des coordonnées GPS en appliquant un flou géographique
     * Retourne des coordonnées approximatives au lieu des coordonnées exactes
     * 
     * @param float $latitude
     * @param float $longitude
     * @param float $radiusKm Rayon d'anonymisation en km (défaut: 0.5km)
     * @return array ['latitude' => float, 'longitude' => float, 'precision' => string]
     */
    public function anonymizeCoordinates(float $latitude, float $longitude, float $radiusKm = self::ANONYMIZATION_RADIUS_KM): array
    {
        // Convertir le rayon en degrés (approximativement)
        // 1 degré de latitude ≈ 111 km
        // 1 degré de longitude varie selon la latitude
        $latOffset = ($radiusKm / 111.0) * (rand(-100, 100) / 100);
        $lngOffset = ($radiusKm / (111.0 * cos(deg2rad($latitude)))) * (rand(-100, 100) / 100);

        // Appliquer l'offset aléatoire
        $anonymizedLat = $latitude + $latOffset;
        $anonymizedLng = $longitude + $lngOffset;

        // Arrondir à 3 décimales (précision ~111m)
        $anonymizedLat = round($anonymizedLat, 3);
        $anonymizedLng = round($anonymizedLng, 3);

        return [
            'latitude' => $anonymizedLat,
            'longitude' => $anonymizedLng,
            'precision' => 'approximative', // Indicateur de précision
            'radius_km' => $radiusKm,
        ];
    }

    /**
     * Obtient le quartier/commune à partir de coordonnées GPS
     * Utilise l'API de géocodage inverse (Nominatim OpenStreetMap)
     * 
     * @param float $latitude
     * @param float $longitude
     * @return array ['quartier' => string, 'commune' => string, 'ville' => string]
     */
    public function getLocationArea(float $latitude, float $longitude): array
    {
        try {
            // Utiliser Nominatim (OpenStreetMap) pour le géocodage inverse
            $response = Http::timeout(5)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 16, // Niveau quartier
                'addressdetails' => 1,
                'accept-language' => 'fr',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $address = $data['address'] ?? [];

                return [
                    'quartier' => $address['suburb'] ?? $address['neighbourhood'] ?? 'Non identifié',
                    'commune' => $address['city_district'] ?? $address['municipality'] ?? 'Non identifiée',
                    'ville' => $address['city'] ?? $address['town'] ?? $address['village'] ?? 'Non identifiée',
                    'pays' => $address['country'] ?? 'Guinée',
                    'display_name' => $data['display_name'] ?? null,
                ];
            }

            return $this->getDefaultLocationArea();
        } catch (\Exception $e) {
            Log::error('Erreur géocodage inverse', [
                'lat' => $latitude,
                'lng' => $longitude,
                'error' => $e->getMessage(),
            ]);

            return $this->getDefaultLocationArea();
        }
    }

    /**
     * Prépare des coordonnées pour stockage sécurisé
     * Combine anonymisation + identification de zone
     * 
     * @param float $latitude
     * @param float $longitude
     * @param int|null $villeId
     * @param bool $anonymize Si true, applique l'anonymisation
     * @return array
     */
    public function prepareSecureLocation(
        float $latitude, 
        float $longitude, 
        ?int $villeId = null,
        bool $anonymize = true
    ): array {
        // Déterminer la zone géographique
        $locationArea = $this->getLocationArea($latitude, $longitude);

        // Anonymiser les coordonnées si demandé
        if ($anonymize) {
            $coords = $this->anonymizeCoordinates($latitude, $longitude);
        } else {
            $coords = [
                'latitude' => round($latitude, 6),
                'longitude' => round($longitude, 6),
                'precision' => 'exacte',
                'radius_km' => 0,
            ];
        }

        // Détecter automatiquement la ville si non fournie
        if (!$villeId && $locationArea['ville'] !== 'Non identifiée') {
            $ville = Ville::where('name', 'LIKE', '%' . $locationArea['ville'] . '%')->first();
            $villeId = $ville?->id;
        }

        return [
            'latitude' => $coords['latitude'],
            'longitude' => $coords['longitude'],
            'precision' => $coords['precision'],
            'radius_km' => $coords['radius_km'],
            'quartier' => $locationArea['quartier'],
            'commune' => $locationArea['commune'],
            'ville_detectee' => $locationArea['ville'],
            'ville_id' => $villeId,
            'location_area_full' => $locationArea,
        ];
    }

    /**
     * Calcule la distance entre deux points GPS (formule de Haversine)
     * 
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float Distance en kilomètres
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Rayon de la Terre en km

        $latDiff = deg2rad($lat2 - $lat1);
        $lngDiff = deg2rad($lng2 - $lng1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDiff / 2) * sin($lngDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Vérifie si des coordonnées sont dans les limites de la Guinée
     * 
     * @param float $latitude
     * @param float $longitude
     * @return bool
     */
    public function isInGuinea(float $latitude, float $longitude): bool
    {
        // Limites approximatives de la Guinée
        // Latitude: 7.19° N à 12.68° N
        // Longitude: -15.08° W à -7.65° W
        
        return $latitude >= 7.19 && $latitude <= 12.68 &&
               $longitude >= -15.08 && $longitude <= -7.65;
    }

    /**
     * Valide des coordonnées GPS
     * 
     * @param float $latitude
     * @param float $longitude
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateCoordinates(float $latitude, float $longitude): array
    {
        $errors = [];

        // Validation des plages générales
        if ($latitude < -90 || $latitude > 90) {
            $errors[] = 'Latitude invalide (doit être entre -90 et 90)';
        }

        if ($longitude < -180 || $longitude > 180) {
            $errors[] = 'Longitude invalide (doit être entre -180 et 180)';
        }

        // Avertissement si hors de Guinée
        if (!$this->isInGuinea($latitude, $longitude)) {
            $errors[] = 'Coordonnées hors du territoire guinéen';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'in_guinea' => $this->isInGuinea($latitude, $longitude),
        ];
    }

    /**
     * Zone par défaut en cas d'erreur
     */
    private function getDefaultLocationArea(): array
    {
        return [
            'quartier' => 'Non identifié',
            'commune' => 'Non identifiée',
            'ville' => 'Non identifiée',
            'pays' => 'Guinée',
            'display_name' => null,
        ];
    }
}
