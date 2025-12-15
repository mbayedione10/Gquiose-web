
<x-filament::page>
    @php
        // Récupérer les filtres
        $dateDebut = $this->dateDebut ?? now()->subDays(30)->format('Y-m-d');
        $dateFin = $this->dateFin ?? now()->format('Y-m-d');
        $contexte = $this->contexte ?? 'all';

        // Statistiques filtrées
        $query = \App\Models\Evaluation::whereBetween('created_at', [
            \Carbon\Carbon::parse($dateDebut)->startOfDay(),
            \Carbon\Carbon::parse($dateFin)->endOfDay(),
        ]);

        if ($contexte !== 'all') {
            $query->where('contexte', $contexte);
        }

        $evaluations = $query->get();

        // Statistiques globales
        $totalUtilisateurs = \App\Models\Utilisateur::count();
        $utilisateursActifs = \App\Models\Utilisateur::where('status', true)->count();
        $totalAlertes = \App\Models\Alerte::count();
        $alertesConfirmees = \App\Models\Alerte::where('etat', 'Confirmée')->count();
        $totalArticles = \App\Models\Article::count();
        $totalVideos = \App\Models\Video::count();
        $totalStructures = \App\Models\Structure::count();
        $totalQuestions = \App\Models\Question::count();

        // Notifications récentes
        $notificationsRecentes = \App\Models\PushNotification::latest()->take(5)->get();

        // Cycles menstruels actifs
        $cyclesActifs = \App\Models\MenstrualCycle::where('is_active', true)->count();

        // Stats par contexte pour les évaluations
        $statsByContexte = \App\Models\Evaluation::whereBetween('created_at', [
                \Carbon\Carbon::parse($dateDebut)->startOfDay(),
                \Carbon\Carbon::parse($dateFin)->endOfDay(),
            ])
            ->when($contexte !== 'all', fn($q) => $q->where('contexte', $contexte))
            ->selectRaw('contexte, COUNT(*) as total')
            ->groupBy('contexte')
            ->get();

        $scoresByType = \App\Models\Evaluation::whereBetween('created_at', [
                \Carbon\Carbon::parse($dateDebut)->startOfDay(),
                \Carbon\Carbon::parse($dateFin)->endOfDay(),
            ])
            ->when($contexte !== 'all', fn($q) => $q->where('contexte', $contexte))
            ->selectRaw('contexte, AVG(score_global) as avg_score, COUNT(*) as total')
            ->whereNotNull('score_global')
            ->groupBy('contexte')
            ->get();

        $evolution = \App\Models\Evaluation::selectRaw('DATE(created_at) as date, COUNT(*) as total, AVG(score_global) as avg_score')
            ->whereBetween('created_at', [
                \Carbon\Carbon::parse($dateDebut)->startOfDay(),
                \Carbon\Carbon::parse($dateFin)->endOfDay(),
            ])
            ->when($contexte !== 'all', fn($q) => $q->where('contexte', $contexte))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topQuestions = \App\Models\QuestionEvaluation::withCount(['reponsesEvaluations' => function($q) use ($dateDebut, $dateFin) {
                $q->whereBetween('created_at', [
                    \Carbon\Carbon::parse($dateDebut)->startOfDay(),
                    \Carbon\Carbon::parse($dateFin)->endOfDay(),
                ]);
            }])
            ->orderBy('reponses_evaluations_count', 'desc')
            ->take(5)
            ->get();
        // Alertes récentes
        $alertesRecentes = \App\Models\Alerte::with(['utilisateur', 'ville'])
            ->latest()
            ->take(7)
            ->get();

        // Distribution des alertes par type (utilisant la relation typeAlerte)
        $alertesParType = \App\Models\Alerte::with('typeAlerte')
            ->get()
            ->groupBy('type_alerte_id')
            ->map(function ($alertes) {
                $typeAlerte = $alertes->first()->typeAlerte;
                return (object) [
                    'type_alerte_id' => $alertes->first()->type_alerte_id,
                    'type_name' => $typeAlerte?->name ?? null,
                    'total' => $alertes->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();
    @endphp

    <!-- En-tête avec filtres -->
    <div class="mb-6 rounded-2xl shadow-2xl p-8" style="background: linear-gradient(to right, #4f46e5, #9333ea, #ec4899);">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2" style="color: #ffffff;">
                    📊 Statistiques
                </h1>
            </div>
        </div>
    </div>

    <!-- Métriques clés - Grid moderne -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Évaluations -->
        <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all duration-300" style="background-color: #ffffff; border-left: 4px solid #6366f1;">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #6366f1, #9333ea);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #4f46e5;">{{ number_format($evaluations->count()) }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium" style="color: #374151;">Total Évaluations</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #e0e7ff; color: #4338ca;">
                    Période sélectionnée
                </div>
            </div>
        </div>

        <!-- Score Moyen Global -->
        <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all duration-300" style="background-color: #ffffff; border-left: 4px solid #10b981;">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #10b981, #059669);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #059669;">{{ $evaluations->avg('score_global') ? number_format($evaluations->avg('score_global'), 2) : '0.00' }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium" style="color: #374151;">Score Moyen Global</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #d1fae5; color: #065f46;">
                    Sur 5.00
                </div>
            </div>
        </div>

        <!-- Questions Évaluées -->
        <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all duration-300" style="background-color: #ffffff; border-left: 4px solid #f97316;">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #f97316, #f59e0b);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #ea580c;">{{ number_format($totalQuestions) }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium" style="color: #374151;">Quizz au Total</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #ffedd5; color: #9a3412;">
                    Bibliothèque complète des quizz
                </div>
            </div>
        </div>

        <!-- Cycles Menstruels -->
        <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all duration-300" style="background-color: #ffffff; border-left: 4px solid #ec4899;">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #ec4899, #f43f5e);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #db2777;">{{ number_format($cyclesActifs) }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium" style="color: #374151;">Cycles Actifs</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #fce7f3; color: #9f1239;">
                    Suivi en cours
                </div>
            </div>
        </div>
    </div>

    

    <!-- Questions les plus populaires -->
    <div class="mb-6">
        <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
            <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">🔥 Top 5 Questions les Plus Évaluées</h3>
            <div class="space-y-3">
                @forelse($topQuestions as $index => $question)
                    <a href="{{ route('filament.resources.question-evaluations.edit', ['record' => $question->id]) }}"
                       class="flex items-center gap-4 p-4 rounded-lg transition-all duration-200 hover:shadow-md"
                       style="background-color: #f9fafb; text-decoration: none; display: flex;">
                        <div class="flex-shrink-0 rounded-full flex items-center justify-center font-bold text-lg"
                             style="width: 2.5rem; height: 2.5rem; background: linear-gradient(to bottom right, #6366f1, #9333ea); color: #ffffff;">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold mb-1" style="color: #111827;">{{ $question->libelle }}</div>
                            <div class="flex items-center gap-4 text-xs">
                                <span class="px-2 py-1 rounded-full" style="background-color: #dbeafe; color: #1e40af;">
                                    <strong>{{ $question->reponses_evaluations_count }}</strong> réponses
                                </span>
                                @if($question->contexte)
                                    <span class="px-2 py-1 rounded-full" style="background-color: #f3e8ff; color: #6b21a8;">
                                        {{ ucfirst($question->contexte) }}
                                    </span>
                                @endif
                                @if($question->question)
                                    <span>
                                        Question: {{ ucfirst ($question->question) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8" style="color: #9ca3af;">
                        Aucune question évaluée pour la période sélectionnée
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Alertes récentes et Distribution -->
    <div class="mb-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Alertes récentes -->
        <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
            <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">🚨 7 Dernières Alertes</h3>
            <div class="space-y-3">
                @forelse($alertesRecentes as $alerte)
                    <a href="{{ route('filament.resources.alertes.edit', ['record' => $alerte->id]) }}"
                       class="flex items-start gap-3 p-3 rounded-lg transition-all duration-200 hover:shadow-md"
                       style="background-color: #f9fafb; text-decoration: none; display: flex;">
                        <div class="flex-shrink-0 rounded-lg flex items-center justify-center"
                             style="width: 2.5rem; height: 2.5rem; background: linear-gradient(to bottom right, #f43f5e, #dc2626);">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold" style="color: #111827;">{{ $alerte->type }}</span>
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: {{ $alerte->etat === 'Confirmée' ? '#dcfce7' : '#fef3c7' }};
                                             color: {{ $alerte->etat === 'Confirmée' ? '#166534' : '#92400e' }};">
                                    {{ $alerte->etat ?? 'En attente' }}
                                </span>
                            </div>
                            <div class="text-sm mb-2" style="color: #6b7280;">{{ Str::limit($alerte->description, 80) }}</div>
                            <div class="flex items-center gap-3 text-xs" style="color: #9ca3af;">
                                <span>
                                    👤 {{ $alerte->utilisateur?->name ?? 'Utilisateur inconnu' }}
                                </span>
                                <span>
                                    📍 {{ $alerte->ville?->nom ?? 'Ville inconnue' }}
                                </span>
                                <span>
                                    🕐 {{ $alerte->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8" style="color: #9ca3af;">
                        Aucune alerte récente
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Distribution des alertes -->
        <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold" style="color: #1f2937;">📊 Alertes par Type de Violence</h3>
                <div class="px-3 py-1 rounded-full text-sm font-bold" style="background-color: #fee2e2; color: #991b1b;">
                    Total: {{ number_format($totalAlertes) }}
                </div>
            </div>

            <div class="space-y-4" id="alertes-container">
                @forelse($alertesParType as $index => $typeAlerte)
                    @php
                        $percentage = $totalAlertes > 0 ? round(($typeAlerte->total / $totalAlertes * 100), 1) : 0;
                        $isHidden = $index >= 5; // Cacher après les 5 premiers

                        // Mapping des types d'alertes de la BDD vers les configurations d'icônes
                        $typeMapping = [
                            // VBG Traditionnels
                            'Violence Conjugale' => ['icon' => '👊', 'color' => '#dc2626', 'bg' => '#fee2e2', 'gradient' => 'linear-gradient(to right, #ef4444, #dc2626)'],
                            'Harcèlement Sexuel' => ['icon' => '⚠️', 'color' => '#9333ea', 'bg' => '#f3e8ff', 'gradient' => 'linear-gradient(to right, #a855f7, #9333ea)'],
                            'Agression Sexuelle' => ['icon' => '⚠️', 'color' => '#991b1b', 'bg' => '#fecaca', 'gradient' => 'linear-gradient(to right, #dc2626, #991b1b)'],
                            'Mariage Forcé' => ['icon' => '💔', 'color' => '#be185d', 'bg' => '#fce7f3', 'gradient' => 'linear-gradient(to right, #db2777, #be185d)'],
                            'MGF (Excision)' => ['icon' => '🚫', 'color' => '#7c2d12', 'bg' => '#fed7aa', 'gradient' => 'linear-gradient(to right, #ea580c, #7c2d12)'],
                            'Violence Scolaire' => ['icon' => '🏫', 'color' => '#b45309', 'bg' => '#fef3c7', 'gradient' => 'linear-gradient(to right, #f59e0b, #b45309)'],
                            'Exploitation Sexuelle' => ['icon' => '⛓️', 'color' => '#6b21a8', 'bg' => '#f3e8ff', 'gradient' => 'linear-gradient(to right, #9333ea, #6b21a8)'],

                            // Violences Numériques
                            'Cyberharcèlement' => ['icon' => '💻', 'color' => '#0891b2', 'bg' => '#cffafe', 'gradient' => 'linear-gradient(to right, #06b6d4, #0891b2)'],
                            'Harcèlement par Messagerie (SMS/Appels)' => ['icon' => '📱', 'color' => '#0e7490', 'bg' => '#cffafe', 'gradient' => 'linear-gradient(to right, #06b6d4, #0e7490)'],
                            'Diffusion Images Intimes (Revenge Porn)' => ['icon' => '📸', 'color' => '#9f1239', 'bg' => '#ffe4e6', 'gradient' => 'linear-gradient(to right, #f43f5e, #9f1239)'],
                            'Chantage / Extorsion en Ligne' => ['icon' => '💰', 'color' => '#065f46', 'bg' => '#d1fae5', 'gradient' => 'linear-gradient(to right, #10b981, #065f46)'],
                            'Cyberstalking / Surveillance Numérique' => ['icon' => '👁️', 'color' => '#6b21a8', 'bg' => '#f3e8ff', 'gradient' => 'linear-gradient(to right, #a855f7, #6b21a8)'],
                            'Usurpation d\'Identité en Ligne' => ['icon' => '🎭', 'color' => '#7c2d12', 'bg' => '#ffedd5', 'gradient' => 'linear-gradient(to right, #f97316, #7c2d12)'],
                            'Création de Faux Profils pour Harceler' => ['icon' => '👤', 'color' => '#831843', 'bg' => '#fce7f3', 'gradient' => 'linear-gradient(to right, #ec4899, #831843)'],
                            'Hacking / Violation Vie Privée' => ['icon' => '🔓', 'color' => '#991b1b', 'bg' => '#fee2e2', 'gradient' => 'linear-gradient(to right, #ef4444, #991b1b)'],
                            'Menaces en Ligne' => ['icon' => '⚡', 'color' => '#b91c1c', 'bg' => '#fecaca', 'gradient' => 'linear-gradient(to right, #f87171, #b91c1c)'],
                            'Deepfake / Manipulation Média' => ['icon' => '🎬', 'color' => '#4c1d95', 'bg' => '#ede9fe', 'gradient' => 'linear-gradient(to right, #8b5cf6, #4c1d95)'],
                            'Arnaque Sentimentale en Ligne (Romance Scam)' => ['icon' => '💔', 'color' => '#be123c', 'bg' => '#ffe4e6', 'gradient' => 'linear-gradient(to right, #f43f5e, #be123c)'],
                            'Exploitation Sexuelle via Internet' => ['icon' => '🌐', 'color' => '#7c2d12', 'bg' => '#fed7aa', 'gradient' => 'linear-gradient(to right, #fb923c, #7c2d12)'],

                            // Autres
                            'Autres Violences' => ['icon' => '⚠️', 'color' => '#6b7280', 'bg' => '#f3f4f6', 'gradient' => 'linear-gradient(to right, #9ca3af, #6b7280)'],
                        ];

                        $typeName = $typeAlerte->type_name;
                        $config = $typeMapping[$typeName] ?? ['icon' => '❓', 'color' => '#6b7280', 'bg' => '#f3f4f6', 'gradient' => 'linear-gradient(to right, #9ca3af, #6b7280)'];
                        $displayName = $typeName ?? 'Type Non Classifié';
                    @endphp

                    <div class="alerte-item p-4 rounded-lg border-l-4 transition-all duration-200 hover:shadow-md {{ $isHidden ? 'hidden' : '' }}"
                         style="background-color: #f9fafb; border-color: {{ $config['color'] }};"
                         data-index="{{ $index }}">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex-shrink-0 flex items-center justify-center"
                                 style="width: 2.5rem; height: 2.5rem; background: {{ $config['gradient'] }}; border-radius: 0.5rem;">
                                <span style="font-size: 1.25rem;">{{ $config['icon'] }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-lg" style="color: {{ $config['color'] }};">
                                        {{ $displayName }}
                                    </span>
                                    <span class="text-2xl font-bold" style="color: {{ $config['color'] }};">
                                        {{ number_format($typeAlerte->total) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <!-- Barre de progression -->
                            <div class="w-full rounded-full overflow-hidden" style="height: 0.75rem; background-color: {{ $config['bg'] }};">
                                <div class="h-full rounded-full transition-all duration-500 flex items-center justify-end px-2"
                                     style="background: {{ $config['gradient'] }}; width: {{ $percentage }}%">
                                    <span class="text-xs font-bold" style="color: #ffffff;">{{ $percentage }}%</span>
                                </div>
                            </div>

                            <!-- Détails -->
                            <div class="flex items-center justify-between text-sm">
                                <span style="color: #6b7280;">
                                    <strong>{{ $typeAlerte->total }}</strong> {{ $typeAlerte->total > 1 ? 'cas signalés' : 'cas signalé' }}
                                </span>
                                <span class="font-medium" style="color: {{ $config['color'] }};">
                                    {{ $percentage }}% du total
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 rounded-lg" style="background-color: #f9fafb;">
                        <div class="text-4xl mb-2">📊</div>
                        <div style="color: #9ca3af;">Aucune donnée disponible</div>
                    </div>
                @endforelse

                <!-- Bouton Voir Plus / Voir Moins -->
                @if($alertesParType->count() > 5)
                    <div class="text-center mt-4">
                        <button id="toggle-alertes-btn"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-medium transition-all duration-200 hover:shadow-lg"
                                style="background: linear-gradient(to right, #6366f1, #8b5cf6); color: #ffffff;"
                                onclick="toggleAlertes()">
                            <span id="btn-text">Voir plus</span>
                            <span id="btn-count" class="px-2 py-0.5 rounded-full text-xs font-bold" style="background-color: rgba(255, 255, 255, 0.2);">
                                +{{ $alertesParType->count() - 5 }}
                            </span>
                            <svg id="btn-icon-down" style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            <svg id="btn-icon-up" class="hidden" style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                @if($alertesParType->isNotEmpty())
                    @php
                        $hasNonClassified = $alertesParType->contains('type_name', null);
                        $nonClassifiedCount = $hasNonClassified ? $alertesParType->firstWhere('type_name', null)?->total : 0;
                    @endphp

                    <!-- Avertissement si alertes non classifiées -->
                    @if($hasNonClassified && $nonClassifiedCount > 0)
                        <div class="mt-4 p-4 rounded-lg" style="background: linear-gradient(to right, #fee2e2, #fecaca); border: 2px solid #ef4444;">
                            <div class="flex items-start gap-3">
                                <div style="font-size: 1.5rem;">⚠️</div>
                                <div class="flex-1 text-sm" style="color: #991b1b;">
                                    <strong>Attention:</strong> {{ number_format($nonClassifiedCount) }} {{ $nonClassifiedCount > 1 ? 'alertes ne sont pas classifiées' : 'alerte n\'est pas classifiée' }}.
                                    Il est recommandé de définir un type de violence pour chaque alerte afin d'améliorer l'analyse des données.
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Résumé -->
                    <div class="mt-4 p-4 rounded-lg" style="background: linear-gradient(to right, #fef3c7, #fde68a); border: 1px solid #fbbf24;">
                        <div class="flex items-start gap-3">
                            <div style="font-size: 1.5rem;">ℹ️</div>
                            <div class="flex-1 text-sm" style="color: #92400e;">
                                <strong>Résumé:</strong> {{ number_format($totalAlertes) }} alertes signalées au total,
                                réparties sur {{ $alertesParType->count() }} {{ $alertesParType->count() > 1 ? 'catégories' : 'catégorie' }}.
                            </div>
                        </div>
                    </div>

                    <!-- Bouton d'action -->
                    <div class="mt-4 pt-4" style="border-top: 2px solid #e5e7eb;">
                        <div class="text-center">
                            <a href="{{ route('filament.resources.alertes.index') }}"
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-bold transition-all duration-200 hover:shadow-lg"
                               style="background: linear-gradient(to right, #f43f5e, #dc2626); color: #ffffff; text-decoration: none;">
                                📋 Voir toutes les alertes
                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notifications récentes -->
    <div class="mb-6">
        <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
            <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">🔔 5 Dernières Notifications Push</h3>
            <div class="space-y-3">
                @forelse($notificationsRecentes as $notif)
                    <div class="flex items-start gap-3 p-3 rounded-lg transition-colors" style="background-color: #f9fafb;">
                        <div class="flex-shrink-0 rounded-lg flex items-center justify-center" style="width: 2.5rem; height: 2.5rem; background: linear-gradient(to bottom right, #06b6d4, #2563eb);">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold" style="color: #111827;">{{ $notif->title }}</div>
                            <div class="text-sm" style="color: #6b7280;">{{ $notif->body }}</div>
                            <div class="text-xs mt-1" style="color: #9ca3af;">
                                {{ $notif->created_at->diffForHumans() }} • Type: {{ $notif->type }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8" style="color: #9ca3af;">
                        Aucune notification récente
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @php
        // Statistiques des notifications
        $notificationStats = [
            'total' => \App\Models\NotificationLog::count(),
            'sent' => \App\Models\NotificationLog::where('status', 'sent')->count(),
            'delivered' => \App\Models\NotificationLog::where('status', 'delivered')->count(),
            'opened' => \App\Models\NotificationLog::whereNotNull('opened_at')->count(),
            'clicked' => \App\Models\NotificationLog::whereNotNull('clicked_at')->count(),
            'failed' => \App\Models\NotificationLog::where('status', 'failed')->count(),
        ];

        // Stats par statut
        $statsByStatus = \App\Models\NotificationLog::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        // Stats par catégorie
        $statsByCategory = \App\Models\NotificationLog::selectRaw('category, COUNT(*) as total')
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        // Stats par plateforme
        $statsByPlatform = \App\Models\NotificationLog::selectRaw('platform, COUNT(*) as total')
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->get();
    @endphp

    <!-- Statistiques des Notifications -->
    <div class="mb-6">
        <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold" style="color: #1f2937;">📊 Statistiques des Notifications</h3>
                <div class="px-3 py-1 rounded-full text-sm font-bold" style="background-color: #dbeafe; color: #1e40af;">
                    Total: {{ number_format($notificationStats['total']) }}
                </div>
            </div>

            <!-- Métriques clés des notifications -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- Total -->
                <div class="p-4 rounded-lg text-center" style="background: linear-gradient(to bottom right, #dbeafe, #bfdbfe); border: 2px solid #3b82f6;">
                    <div class="text-3xl font-bold mb-1" style="color: #1e40af;">{{ number_format($notificationStats['total']) }}</div>
                    <div class="text-xs font-medium" style="color: #1e3a8a;">Total</div>
                </div>

                <!-- Envoyées -->
                <div class="p-4 rounded-lg text-center" style="background: linear-gradient(to bottom right, #fef3c7, #fde68a); border: 2px solid #f59e0b;">
                    <div class="text-3xl font-bold mb-1" style="color: #92400e;">{{ number_format($notificationStats['sent']) }}</div>
                    <div class="text-xs font-medium" style="color: #78350f;">Envoyées</div>
                </div>

                <!-- Livrées -->
                <div class="p-4 rounded-lg text-center" style="background: linear-gradient(to bottom right, #d1fae5, #a7f3d0); border: 2px solid #10b981;">
                    <div class="text-3xl font-bold mb-1" style="color: #065f46;">{{ number_format($notificationStats['delivered']) }}</div>
                    <div class="text-xs font-medium" style="color: #064e3b;">Livrées</div>
                </div>

                <!-- Ouvertes -->
                <div class="p-4 rounded-lg text-center" style="background: linear-gradient(to bottom right, #e0e7ff, #c7d2fe); border: 2px solid #6366f1;">
                    <div class="text-3xl font-bold mb-1" style="color: #4338ca;">{{ number_format($notificationStats['opened']) }}</div>
                    <div class="text-xs font-medium" style="color: #3730a3;">Ouvertes</div>
                </div>

                <!-- Cliquées -->
                <div class="p-4 rounded-lg text-center" style="background: linear-gradient(to bottom right, #fce7f3, #fbcfe8); border: 2px solid #ec4899;">
                    <div class="text-3xl font-bold mb-1" style="color: #9f1239;">{{ number_format($notificationStats['clicked']) }}</div>
                    <div class="text-xs font-medium" style="color: #831843;">Cliquées</div>
                </div>

                <!-- Échouées -->
                <div class="p-4 rounded-lg text-center" style="background: linear-gradient(to bottom right, #fee2e2, #fecaca); border: 2px solid #ef4444;">
                    <div class="text-3xl font-bold mb-1" style="color: #991b1b;">{{ number_format($notificationStats['failed']) }}</div>
                    <div class="text-xs font-medium" style="color: #7f1d1d;">Échouées</div>
                </div>
            </div>

            <!-- Graphiques des notifications -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Par Statut -->
                <div class="rounded-lg p-4" style="background-color: #f9fafb;">
                    <h4 class="text-md font-bold mb-3" style="color: #374151;">📈 Par Statut</h4>
                    <div class="space-y-2">
                        @forelse($statsByStatus as $stat)
                            @php
                                $percentage = $notificationStats['total'] > 0 ? round(($stat->total / $notificationStats['total']) * 100, 1) : 0;
                                $statusConfig = [
                                    'sent' => ['label' => 'Envoyées', 'color' => '#f59e0b', 'bg' => '#fef3c7'],
                                    'delivered' => ['label' => 'Livrées', 'color' => '#10b981', 'bg' => '#d1fae5'],
                                    'opened' => ['label' => 'Ouvertes', 'color' => '#6366f1', 'bg' => '#e0e7ff'],
                                    'clicked' => ['label' => 'Cliquées', 'color' => '#ec4899', 'bg' => '#fce7f3'],
                                    'failed' => ['label' => 'Échouées', 'color' => '#ef4444', 'bg' => '#fee2e2'],
                                ];
                                $config = $statusConfig[$stat->status] ?? ['label' => ucfirst($stat->status), 'color' => '#6b7280', 'bg' => '#f3f4f6'];
                            @endphp
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span style="color: #374151;">{{ $config['label'] }}</span>
                                    <span class="font-bold" style="color: {{ $config['color'] }};">{{ number_format($stat->total) }} ({{ $percentage }}%)</span>
                                </div>
                                <div class="w-full rounded-full overflow-hidden" style="height: 0.5rem; background-color: {{ $config['bg'] }};">
                                    <div class="h-full rounded-full" style="background-color: {{ $config['color'] }}; width: {{ $percentage }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-xs py-4" style="color: #9ca3af;">Aucune donnée</div>
                        @endforelse
                    </div>
                </div>

                <!-- Par Catégorie -->
                <div class="rounded-lg p-4" style="background-color: #f9fafb;">
                    <h4 class="text-md font-bold mb-3" style="color: #374151;">📂 Par Catégorie</h4>
                    <div class="space-y-2">
                        @forelse($statsByCategory as $stat)
                            @php
                                $percentage = $notificationStats['total'] > 0 ? round(($stat->total / $notificationStats['total']) * 100, 1) : 0;
                                $categoryConfig = [
                                    'alert' => ['icon' => '🚨', 'color' => '#ef4444', 'label' => 'Alerte'],
                                    'reminder' => ['icon' => '⏰', 'color' => '#f59e0b', 'label' => 'Rappel'],
                                    'health_tip' => ['icon' => '💡', 'color' => '#10b981', 'label' => 'Conseil Santé'],
                                    'cycle' => ['icon' => '🩸', 'color' => '#ec4899', 'label' => 'Cycle'],
                                    'general' => ['icon' => '📢', 'color' => '#6366f1', 'label' => 'Général'],
                                    'quiz' => ['icon' => '❓', 'color' => '#8b5cf6', 'label' => 'Quiz'],
                                    'article' => ['icon' => '📚', 'color' => '#06b6d4', 'label' => 'Article'],
                                    'video' => ['icon' => '🎬', 'color' => '#f97316', 'label' => 'Vidéo'],
                                ];
                                $config = $categoryConfig[$stat->category] ?? ['icon' => '📌', 'color' => '#6b7280', 'label' => ucfirst($stat->category)];
                            @endphp
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span style="color: #374151;">{{ $config['icon'] }} {{ $config['label'] }}</span>
                                    <span class="font-bold" style="color: {{ $config['color'] }};">{{ number_format($stat->total) }} ({{ $percentage }}%)</span>
                                </div>
                                <div class="w-full rounded-full overflow-hidden" style="height: 0.5rem; background-color: #e5e7eb;">
                                    <div class="h-full rounded-full" style="background-color: {{ $config['color'] }}; width: {{ $percentage }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-xs py-4" style="color: #9ca3af;">Aucune donnée</div>
                        @endforelse
                    </div>
                </div>

                <!-- Par Plateforme -->
                <div class="rounded-lg p-4" style="background-color: #f9fafb;">
                    <h4 class="text-md font-bold mb-3" style="color: #374151;">📱 Par Plateforme</h4>
                    <div class="space-y-2">
                        @forelse($statsByPlatform as $stat)
                            @php
                                $percentage = $notificationStats['total'] > 0 ? round(($stat->total / $notificationStats['total']) * 100, 1) : 0;
                                $platformConfig = [
                                    'android' => ['icon' => '🤖', 'color' => '#10b981'],
                                    'ios' => ['icon' => '🍎', 'color' => '#6b7280'],
                                    'web' => ['icon' => '🌐', 'color' => '#3b82f6'],
                                ];
                                $config = $platformConfig[$stat->platform] ?? ['icon' => '📱', 'color' => '#6b7280'];
                            @endphp
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span style="color: #374151;">{{ $config['icon'] }} {{ ucfirst($stat->platform) }}</span>
                                    <span class="font-bold" style="color: {{ $config['color'] }};">{{ number_format($stat->total) }} ({{ $percentage }}%)</span>
                                </div>
                                <div class="w-full rounded-full overflow-hidden" style="height: 0.5rem; background-color: #e5e7eb;">
                                    <div class="h-full rounded-full" style="background-color: {{ $config['color'] }}; width: {{ $percentage }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-xs py-4" style="color: #9ca3af;">Aucune donnée</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Bouton détails -->
            <div class="mt-6 text-center">
                <a href="{{ route('filament.resources.notification-logs.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-bold transition-all duration-200 hover:shadow-lg"
                   style="background: linear-gradient(to right, #3b82f6, #2563eb); color: #ffffff; text-decoration: none;">
                    📋 Voir tous les logs de notifications
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // Fonction pour toggle l'affichage des alertes
        function toggleAlertes() {
            const items = document.querySelectorAll('.alerte-item');
            const btnText = document.getElementById('btn-text');
            const btnCount = document.getElementById('btn-count');
            const iconDown = document.getElementById('btn-icon-down');
            const iconUp = document.getElementById('btn-icon-up');

            let isExpanded = false;

            items.forEach((item, index) => {
                if (index >= 5) {
                    if (item.classList.contains('hidden')) {
                        item.classList.remove('hidden');
                        isExpanded = true;
                    } else {
                        item.classList.add('hidden');
                        isExpanded = false;
                    }
                }
            });

            // Mettre à jour le bouton
            if (isExpanded) {
                btnText.textContent = 'Voir moins';
                btnCount.classList.add('hidden');
                iconDown.classList.add('hidden');
                iconUp.classList.remove('hidden');
            } else {
                btnText.textContent = 'Voir plus';
                btnCount.classList.remove('hidden');
                iconDown.classList.remove('hidden');
                iconUp.classList.add('hidden');
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        .apexcharts-tooltip {
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        /* Animation pour les alertes */
        .alerte-item {
            transition: all 0.3s ease-in-out;
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alerte-item.hidden {
            display: none;
        }

        #toggle-alertes-btn:hover {
            transform: translateY(-2px);
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</x-filament::page>
