<?php

namespace App\Services\VBG;

use App\Models\CategorieConseil;
use App\Models\SectionConseil;

class SafetyAdviceService
{
    /**
     * Génère des conseils de sécurité pour une alerte donnée
     *
     * @param \App\Models\Alerte $alerte
     * @return string
     */
    public function getAdviceForAlert($alerte): string
    {
        return $this->generateSafetyAdvice(
            $alerte->type_alerte_id,
            $alerte->sous_type_violence_numerique_id
        );
    }

    /**
     * Génère des conseils de sécurité automatiques basés sur le type de violence
     *
     * @param int|null $typeAlerteId
     * @param int|null $sousTypeId
     * @return string
     */
    public function generateSafetyAdvice(?int $typeAlerteId, ?int $sousTypeId = null): string
    {
        $categorie = $this->findCategorie($typeAlerteId, $sousTypeId);

        if (!$categorie) {
            return $this->getDefaultAdviceText();
        }

        return $this->formatCategorie($categorie);
    }

    /**
     * Trouve la catégorie de conseils appropriée selon la priorité:
     * 1. Sous-type de violence numérique
     * 2. Type d'alerte
     * 3. Catégorie par défaut
     *
     * @param int|null $typeAlerteId
     * @param int|null $sousTypeId
     * @return CategorieConseil|null
     */
    private function findCategorie(?int $typeAlerteId, ?int $sousTypeId): ?CategorieConseil
    {
        // Priorité 1: Sous-type de violence numérique
        if ($sousTypeId) {
            $categorie = CategorieConseil::where('sous_type_violence_numerique_id', $sousTypeId)
                ->where('status', true)
                ->first();

            if ($categorie) {
                return $categorie;
            }
        }

        // Priorité 2: Type d'alerte
        if ($typeAlerteId) {
            $categorie = CategorieConseil::where('type_alerte_id', $typeAlerteId)
                ->where('status', true)
                ->first();

            if ($categorie) {
                return $categorie;
            }
        }

        // Priorité 3: Catégorie par défaut
        return CategorieConseil::where('is_default', true)
            ->where('status', true)
            ->first();
    }

    /**
     * Formate une catégorie de conseils en texte lisible
     *
     * @param CategorieConseil $categorie
     * @return string
     */
    private function formatCategorie(CategorieConseil $categorie): string
    {
        $emoji = $categorie->emoji ?? '⚠️';
        $output = "{$emoji} {$categorie->nom} :\n\n";

        $sections = $categorie->sections()
            ->where('status', true)
            ->orderBy('ordre')
            ->with([
                'items' => fn ($query) => $query
                    ->where('status', true)
                    ->orderBy('ordre')
            ])
            ->get();

        foreach ($sections as $section) {
            $output .= $this->formatSection($section);
        }

        return $output;
    }

    /**
     * Formate une section avec ses items
     *
     * @param SectionConseil $section
     * @return string
     */
    private function formatSection(SectionConseil $section): string
    {
        $emoji = $section->emoji ?? '';
        $output = "{$emoji} {$section->titre} :\n";

        foreach ($section->items as $item) {
            $output .= "• {$item->contenu}\n";
        }

        $output .= "\n";

        return $output;
    }

    /**
     * Retourne un texte par défaut si aucune catégorie n'est trouvée
     * (fallback de sécurité)
     *
     * @return string
     */
    private function getDefaultAdviceText(): string
    {
        return "⚠️ CONSEILS DE SÉCURITÉ GÉNÉRAUX :\n\n" .
            "🔒 SÉCURITÉ IMMÉDIATE :\n" .
            "• Si tu es en danger immédiat, appelle la police (117) ou OPROGEM (116)\n" .
            "• Éloigne-toi de la situation dangereuse si possible\n" .
            "• Parle à une personne de confiance\n\n" .
            "📱 SÉCURITÉ NUMÉRIQUE :\n" .
            "• Ne supprime pas les preuves (messages, photos, emails)\n" .
            "• Fais des captures d'écran de tout\n" .
            "• Sauvegarde les preuves dans un endroit sûr (cloud privé, clé USB cachée)\n\n" .
            "🆘 OBTENIR DE L'AIDE :\n" .
            "• Centre d'Écoute OPROGEM : 116 (gratuit, 24h/24)\n" .
            "• Centre Sabou Guinée : +224 621 000 006\n" .
            "• Guichet Unique VBG CHU Donka : +224 621 000 007\n" .
            "• Utilise l'app GquiOse pour trouver un centre d'aide près de toi\n\n" .
            "⚠️ IMPORTANT : Tes informations sont confidentielles. Tu n'es pas seul.e.";
    }

    /**
     * Récupère toutes les catégories de conseils actives
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCategories()
    {
        return CategorieConseil::where('status', true)
            ->orderBy('ordre')
            ->with([
                'typeAlerte',
                'sousTypeViolenceNumerique',
                'sections' => fn ($query) => $query
                    ->where('status', true)
                    ->orderBy('ordre')
                    ->withCount('items')
            ])
            ->get();
    }

    /**
     * Récupère une catégorie par son ID avec toutes ses sections et items
     *
     * @param int $categorieId
     * @return CategorieConseil|null
     */
    public function getCategorieWithDetails(int $categorieId): ?CategorieConseil
    {
        return CategorieConseil::where('id', $categorieId)
            ->where('status', true)
            ->with([
                'typeAlerte',
                'sousTypeViolenceNumerique',
                'sections' => fn ($query) => $query
                    ->where('status', true)
                    ->orderBy('ordre')
                    ->with([
                        'items' => fn ($q) => $q
                            ->where('status', true)
                            ->orderBy('ordre')
                    ])
            ])
            ->first();
    }

    /**
     * Génère un aperçu des conseils (pour affichage rapide)
     *
     * @param int|null $typeAlerteId
     * @param int|null $sousTypeId
     * @return array
     */
    public function getAdvicePreview(?int $typeAlerteId, ?int $sousTypeId = null): array
    {
        $categorie = $this->findCategorie($typeAlerteId, $sousTypeId);

        if (!$categorie) {
            return [
                'titre' => 'Conseils de sécurité généraux',
                'emoji' => '⚠️',
                'sections_count' => 0,
                'items_count' => 0,
            ];
        }

        $sectionsCount = $categorie->sections()->where('status', true)->count();
        $itemsCount = $categorie->sections()
            ->where('status', true)
            ->withCount(['items' => fn ($q) => $q->where('status', true)])
            ->get()
            ->sum('items_count');

        return [
            'titre' => $categorie->nom,
            'emoji' => $categorie->emoji ?? '⚠️',
            'sections_count' => $sectionsCount,
            'items_count' => $itemsCount,
        ];
    }
}
