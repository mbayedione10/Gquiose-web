
<?php

namespace App\Services\VBG;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class EvidenceSecurityService
{
    /**
     * Upload et sécurise une preuve (chiffrement + suppression EXIF)
     */
    public function secureUpload($file, string $alertRef): array
    {
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $extension = $file->getClientOriginalExtension();
        
        // Générer un nom unique pour le fichier
        $fileName = Str::uuid() . '.' . $extension;
        $relativePath = 'preuves/' . date('Y/m') . '/' . $alertRef;
        
        // Lire le contenu du fichier
        $fileContent = file_get_contents($file->getRealPath());
        
        // Si c'est une image, supprimer les métadonnées EXIF
        if ($this->isImage($mimeType)) {
            $fileContent = $this->removeExifData($file->getRealPath(), $mimeType);
        }
        
        // Chiffrer le contenu avec AES-256
        $encryptedContent = Crypt::encryptString($fileContent);
        
        // Stocker le fichier chiffré
        $fullPath = $relativePath . '/' . $fileName . '.encrypted';
        Storage::disk('local')->put($fullPath, $encryptedContent);
        
        // Retourner les métadonnées (non sensibles)
        return [
            'path' => $fullPath,
            'type' => $mimeType,
            'original_name' => $originalName,
            'size' => strlen($fileContent),
            'uploaded_at' => now()->toDateTimeString(),
            'is_encrypted' => true,
            'exif_removed' => $this->isImage($mimeType)
        ];
    }
    
    /**
     * Déchiffre et récupère une preuve
     */
    public function retrieveEvidence(string $encryptedPath): ?string
    {
        if (!Storage::disk('local')->exists($encryptedPath)) {
            return null;
        }
        
        $encryptedContent = Storage::disk('local')->get($encryptedPath);
        
        try {
            return Crypt::decryptString($encryptedContent);
        } catch (\Exception $e) {
            \Log::error('Erreur déchiffrement preuve: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Supprime une preuve de manière sécurisée
     */
    public function deleteEvidence(string $encryptedPath): bool
    {
        if (!Storage::disk('local')->exists($encryptedPath)) {
            return false;
        }
        
        // Écraser le fichier avec des données aléatoires avant suppression (sécurité renforcée)
        $fileSize = Storage::disk('local')->size($encryptedPath);
        $randomData = random_bytes($fileSize);
        Storage::disk('local')->put($encryptedPath, $randomData);
        
        // Supprimer le fichier
        return Storage::disk('local')->delete($encryptedPath);
    }
    
    /**
     * Supprime toutes les preuves d'une alerte
     */
    public function deleteAllEvidences(array $preuves): void
    {
        foreach ($preuves as $preuve) {
            if (isset($preuve['path'])) {
                $this->deleteEvidence($preuve['path']);
            }
        }
    }
    
    /**
     * Vérifie si le fichier est une image
     */
    private function isImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }
    
    /**
     * Supprime les métadonnées EXIF d'une image
     */
    private function removeExifData(string $filePath, string $mimeType): string
    {
        try {
            // Utiliser Intervention Image pour charger et nettoyer l'image
            $image = Image::make($filePath);
            
            // Encoder l'image sans métadonnées
            // La méthode encode() crée une nouvelle image sans les métadonnées EXIF
            $extension = $this->getMimeExtension($mimeType);
            $cleanedImage = $image->encode($extension, 90); // 90% qualité
            
            return (string) $cleanedImage;
        } catch (\Exception $e) {
            \Log::error('Erreur suppression EXIF: ' . $e->getMessage());
            // En cas d'erreur, retourner le contenu original
            return file_get_contents($filePath);
        }
    }
    
    /**
     * Obtient l'extension à partir du MIME type
     */
    private function getMimeExtension(string $mimeType): string
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        
        return $map[$mimeType] ?? 'jpg';
    }
    
    /**
     * Génère un lien de téléchargement temporaire sécurisé
     */
    public function generateTemporaryDownloadUrl(string $alerteId, int $evidenceIndex, int $expiresInMinutes = 15): string
    {
        $token = Crypt::encryptString(json_encode([
            'alerte_id' => $alerteId,
            'evidence_index' => $evidenceIndex,
            'expires_at' => now()->addMinutes($expiresInMinutes)->timestamp
        ]));
        
        return route('alertes.download-evidence', [
            'alerte' => $alerteId,
            'index' => $evidenceIndex,
            'token' => $token
        ]);
    }
    
    /**
     * Valide un token de téléchargement
     */
    public function validateDownloadToken(string $token, string $alerteId, int $evidenceIndex): bool
    {
        try {
            $data = json_decode(Crypt::decryptString($token), true);
            
            if ($data['alerte_id'] != $alerteId || $data['evidence_index'] != $evidenceIndex) {
                return false;
            }
            
            if ($data['expires_at'] < now()->timestamp) {
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
