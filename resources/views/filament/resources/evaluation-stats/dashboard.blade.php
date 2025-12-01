
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

        // Statistiques des notifications logs
        $totalNotificationsLogs = \App\Models\NotificationLog::count();
        $notificationsEnvoyees = \App\Models\NotificationLog::where('status', 'sent')->count();
        $notificationsLivrees = \App\Models\NotificationLog::where('status', 'delivered')->count();
        $notificationsOuvertes = \App\Models\NotificationLog::where('status', 'opened')->count();
        $notificationsCliquees = \App\Models\NotificationLog::where('status', 'clicked')->count();
        $notificationsEchouees = \App\Models\NotificationLog::where('status', 'failed')->count();

        // Stats par statut
        $notificationsByStatus = \App\Models\NotificationLog::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => $item->total]);

        // Stats par catégorie
        $notificationsByCategory = \App\Models\NotificationLog::selectRaw('category, COUNT(*) as total')
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Stats par plateforme
        $notificationsByPlatform = \App\Models\NotificationLog::selectRaw('platform, COUNT(*) as total')
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->get()
            ->mapWithKeys(fn($item) => [$item->platform => $item->total]);

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

        // Distribution des alertes par type
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

        // Onglet actif
        $activeTab = request()->get('tab', 'dashboard');
    @endphp

    <!-- En-tête avec filtres -->
    <div class="mb-6 rounded-2xl shadow-2xl p-8" style="background: linear-gradient(to right, #4f46e5, #9333ea, #ec4899);">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2" style="color: #ffffff;">
                    📊 Statistiques & Graphiques
                </h1>
            </div>
        </div>
    </div>

    <!-- Navigation par onglets -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" style="overflow-x: auto;">
                <a href="?tab=dashboard" 
                   class="tab-link {{ $activeTab === 'dashboard' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    📊 Tableau de bord
                </a>
                <a href="?tab=notifications" 
                   class="tab-link {{ $activeTab === 'notifications' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    🔔 Notifications
                </a>
                <a href="?tab=information" 
                   class="tab-link {{ $activeTab === 'information' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    ℹ️ Information
                </a>
                <a href="?tab=monitoring" 
                   class="tab-link {{ $activeTab === 'monitoring' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    📈 Monitoring
                </a>
                <a href="?tab=utilisateurs" 
                   class="tab-link {{ $activeTab === 'utilisateurs' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    👥 Utilisateurs
                </a>
                <a href="?tab=faqs" 
                   class="tab-link {{ $activeTab === 'faqs' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    ❓ FAQs
                </a>
                <a href="?tab=videos" 
                   class="tab-link {{ $activeTab === 'videos' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    🎥 Vidéos
                </a>
            </nav>
        </div>
    </div>

    <!-- Contenu selon l'onglet actif -->
    @if($activeTab === 'dashboard')
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
                    <div class="text-sm font-medium" style="color: #374151;">Questions au Total</div>
                    <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #ffedd5; color: #9a3412;">
                        Bibliothèque complète
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

        <!-- Graphiques Statistiques -->
        <div class="mb-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Répartition par contexte -->
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
                <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">📊 Répartition par Contexte</h3>
                <div id="chartTypeRepartition"></div>
            </div>

            <!-- Scores moyens par contexte -->
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
                <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">⭐ Scores Moyens par Contexte</h3>
                <div id="chartScoresMoyens"></div>
            </div>
        </div>

        <!-- Graphique d'évolution -->
        <div class="mb-6">
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
                <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">📈 Évolution Temporelle</h3>
                <div id="chartEvolution"></div>
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
                                    <span>👤 {{ $alerte->utilisateur?->name ?? 'Utilisateur inconnu' }}</span>
                                    <span>📍 {{ $alerte->ville?->nom ?? 'Ville inconnue' }}</span>
                                    <span>🕐 {{ $alerte->created_at->format('d/m/Y H:i') }}</span>
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
                    <h3 class="text-lg font-bold" style="color: #1f2937;">📊 Alertes par Type</h3>
                    <div class="px-3 py-1 rounded-full text-sm font-bold" style="background-color: #fee2e2; color: #991b1b;">
                        Total: {{ number_format($totalAlertes) }}
                    </div>
                </div>
                <div class="space-y-2">
                    @forelse($alertesParType->take(5) as $typeAlerte)
                        @php
                            $percentage = $totalAlertes > 0 ? round(($typeAlerte->total / $totalAlertes * 100), 1) : 0;
                        @endphp
                        <div class="p-3 rounded-lg" style="background-color: #f9fafb;">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium" style="color: #111827;">{{ $typeAlerte->type_name ?? 'Non classifié' }}</span>
                                <span class="font-bold" style="color: #4f46e5;">{{ $typeAlerte->total }}</span>
                            </div>
                            <div class="w-full rounded-full overflow-hidden" style="height: 0.5rem; background-color: #e5e7eb;">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="background: linear-gradient(to right, #6366f1, #9333ea); width: {{ $percentage }}%">
                                </div>
                            </div>
                            <div class="text-xs mt-1" style="color: #6b7280;">{{ $percentage }}%</div>
                        </div>
                    @empty
                        <div class="text-center py-8" style="color: #9ca3af;">Aucune donnée disponible</div>
                    @endforelse
                </div>
            </div>
        </div>
    @elseif($activeTab === 'notifications')
        <!-- Section Notifications -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Notifications -->
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff; border-left: 4px solid #6366f1;">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #6366f1, #9333ea);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #4f46e5;">{{ number_format($totalNotificationsLogs) }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium" style="color: #374151;">Total Notifications</div>
                <a href="{{ route('filament.resources.notification-logs.index') }}" class="mt-2 text-xs px-3 py-1 rounded-full inline-block hover:shadow-md transition-all" style="background-color: #e0e7ff; color: #4338ca; text-decoration: none;">
                    Voir détails →
                </a>
            </div>

            <!-- Notifications Livrées -->
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff; border-left: 4px solid #10b981;">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #10b981, #059669);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #059669;">{{ number_format($notificationsLivrees) }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium" style="color: #374151;">Livrées</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #d1fae5; color: #065f46;">
                    {{ $totalNotificationsLogs > 0 ? number_format(($notificationsLivrees / $totalNotificationsLogs) * 100, 1) : 0 }}% taux
                </div>
            </div>

            <!-- Notifications Ouvertes -->
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff; border-left: 4px solid #f59e0b;">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #f59e0b, #d97706);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #d97706;">{{ number_format($notificationsOuvertes) }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium" style="color: #374151;">Ouvertes</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #fef3c7; color: #92400e;">
                    {{ $notificationsLivrees > 0 ? number_format(($notificationsOuvertes / $notificationsLivrees) * 100, 1) : 0 }}% taux
                </div>
            </div>

            <!-- Notifications Cliquées -->
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff; border-left: 4px solid #8b5cf6;">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #8b5cf6, #7c3aed);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #7c3aed;">{{ number_format($notificationsCliquees) }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium" style="color: #374151;">Cliquées</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #f3e8ff; color: #6b21a8;">
                    {{ $notificationsOuvertes > 0 ? number_format(($notificationsCliquees / $notificationsOuvertes) * 100, 1) : 0 }}% conversion
                </div>
            </div>
        </div>

        <!-- Stats par Statut, Catégorie et Plateforme -->
        <div class="mb-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Par Statut -->
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold" style="color: #1f2937;">📊 Par Statut</h3>
                    <a href="{{ route('filament.resources.notification-logs.index') }}" class="text-xs px-2 py-1 rounded-full hover:shadow-md transition-all" style="background-color: #e0e7ff; color: #4338ca; text-decoration: none;">
                        Détails
                    </a>
                </div>
                <div class="space-y-3">
                    @php
                        $statusConfig = [
                            'sent' => ['label' => 'Envoyées', 'color' => '#3b82f6', 'bg' => '#dbeafe', 'icon' => '📤'],
                            'delivered' => ['label' => 'Livrées', 'color' => '#10b981', 'bg' => '#d1fae5', 'icon' => '✅'],
                            'opened' => ['label' => 'Ouvertes', 'color' => '#f59e0b', 'bg' => '#fef3c7', 'icon' => '👁️'],
                            'clicked' => ['label' => 'Cliquées', 'color' => '#8b5cf6', 'bg' => '#f3e8ff', 'icon' => '🔗'],
                            'failed' => ['label' => 'Échouées', 'color' => '#ef4444', 'bg' => '#fee2e2', 'icon' => '❌'],
                            'pending' => ['label' => 'En attente', 'color' => '#6b7280', 'bg' => '#f3f4f6', 'icon' => '⏳'],
                        ];
                    @endphp
                    @foreach($statusConfig as $status => $config)
                        @php
                            $count = $notificationsByStatus[$status] ?? 0;
                            $percentage = $totalNotificationsLogs > 0 ? round(($count / $totalNotificationsLogs) * 100, 1) : 0;
                        @endphp
                        <div class="p-3 rounded-lg transition-all hover:shadow-md" style="background-color: {{ $config['bg'] }};">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span style="font-size: 1.25rem;">{{ $config['icon'] }}</span>
                                    <span class="font-medium" style="color: #111827;">{{ $config['label'] }}</span>
                                </div>
                                <span class="font-bold" style="color: {{ $config['color'] }};">{{ number_format($count) }}</span>
                            </div>
                            <div class="w-full rounded-full overflow-hidden" style="height: 0.5rem; background-color: #ffffff;">
                                <div class="h-full rounded-full transition-all duration-500" style="background-color: {{ $config['color'] }}; width: {{ $percentage }}%"></div>
                            </div>
                            <div class="text-xs mt-1" style="color: #6b7280;">{{ $percentage }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Par Catégorie -->
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold" style="color: #1f2937;">🏷️ Par Catégorie</h3>
                    <a href="{{ route('filament.resources.notification-logs.index') }}" class="text-xs px-2 py-1 rounded-full hover:shadow-md transition-all" style="background-color: #e0e7ff; color: #4338ca; text-decoration: none;">
                        Détails
                    </a>
                </div>
                <div class="space-y-3">
                    @php
                        $categoryConfig = [
                            'alert' => ['label' => 'Alerte', 'color' => '#ef4444', 'bg' => '#fee2e2', 'icon' => '🚨'],
                            'reminder' => ['label' => 'Rappel', 'color' => '#f59e0b', 'bg' => '#fef3c7', 'icon' => '⏰'],
                            'health_tip' => ['label' => 'Conseil', 'color' => '#10b981', 'bg' => '#d1fae5', 'icon' => '💡'],
                            'cycle' => ['label' => 'Cycle', 'color' => '#ec4899', 'bg' => '#fce7f3', 'icon' => '🩸'],
                            'general' => ['label' => 'Général', 'color' => '#6b7280', 'bg' => '#f3f4f6', 'icon' => '📢'],
                            'quiz' => ['label' => 'Quiz', 'color' => '#8b5cf6', 'bg' => '#f3e8ff', 'icon' => '❓'],
                            'article' => ['label' => 'Article', 'color' => '#3b82f6', 'bg' => '#dbeafe', 'icon' => '📚'],
                            'video' => ['label' => 'Vidéo', 'color' => '#f43f5e', 'bg' => '#ffe4e6', 'icon' => '🎥'],
                        ];
                    @endphp
                    @foreach($notificationsByCategory as $categ)
                        @php
                            $config = $categoryConfig[$categ->category] ?? ['label' => ucfirst($categ->category), 'color' => '#6b7280', 'bg' => '#f3f4f6', 'icon' => '📌'];
                            $percentage = $totalNotificationsLogs > 0 ? round(($categ->total / $totalNotificationsLogs) * 100, 1) : 0;
                        @endphp
                        <div class="p-3 rounded-lg transition-all hover:shadow-md" style="background-color: {{ $config['bg'] }};">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span style="font-size: 1.25rem;">{{ $config['icon'] }}</span>
                                    <span class="font-medium" style="color: #111827;">{{ $config['label'] }}</span>
                                </div>
                                <span class="font-bold" style="color: {{ $config['color'] }};">{{ number_format($categ->total) }}</span>
                            </div>
                            <div class="w-full rounded-full overflow-hidden" style="height: 0.5rem; background-color: #ffffff;">
                                <div class="h-full rounded-full transition-all duration-500" style="background-color: {{ $config['color'] }}; width: {{ $percentage }}%"></div>
                            </div>
                            <div class="text-xs mt-1" style="color: #6b7280;">{{ $percentage }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Par Plateforme -->
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold" style="color: #1f2937;">📱 Par Plateforme</h3>
                    <a href="{{ route('filament.resources.notification-logs.index') }}" class="text-xs px-2 py-1 rounded-full hover:shadow-md transition-all" style="background-color: #e0e7ff; color: #4338ca; text-decoration: none;">
                        Détails
                    </a>
                </div>
                <div class="space-y-3">
                    @php
                        $platformConfig = [
                            'android' => ['label' => 'Android', 'color' => '#10b981', 'bg' => '#d1fae5', 'icon' => '🤖'],
                            'ios' => ['label' => 'iOS', 'color' => '#3b82f6', 'bg' => '#dbeafe', 'icon' => '🍎'],
                        ];
                    @endphp
                    @foreach($platformConfig as $platform => $config)
                        @php
                            $count = $notificationsByPlatform[$platform] ?? 0;
                            $percentage = $totalNotificationsLogs > 0 ? round(($count / $totalNotificationsLogs) * 100, 1) : 0;
                        @endphp
                        <div class="p-4 rounded-lg transition-all hover:shadow-md" style="background-color: {{ $config['bg'] }};">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <span style="font-size: 2rem;">{{ $config['icon'] }}</span>
                                    <div>
                                        <div class="font-bold text-lg" style="color: #111827;">{{ $config['label'] }}</div>
                                        <div class="text-xs" style="color: #6b7280;">{{ $percentage }}% du total</div>
                                    </div>
                                </div>
                                <span class="font-bold text-2xl" style="color: {{ $config['color'] }};">{{ number_format($count) }}</span>
                            </div>
                            <div class="w-full rounded-full overflow-hidden" style="height: 0.75rem; background-color: #ffffff;">
                                <div class="h-full rounded-full transition-all duration-500 flex items-center justify-end px-2" style="background-color: {{ $config['color'] }}; width: {{ $percentage }}%">
                                    <span class="text-xs font-bold" style="color: #ffffff;">{{ $percentage }}%</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Notifications récentes -->
        <div class="mb-6">
            <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold" style="color: #1f2937;">🔔 Notifications Récentes</h3>
                    <a href="{{ route('filament.resources.notification-logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-all hover:shadow-lg" style="background: linear-gradient(to right, #6366f1, #8b5cf6); color: #ffffff; text-decoration: none;">
                        Voir tout
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
                <div class="space-y-3">
                    @php
                        $recentLogs = \App\Models\NotificationLog::with('utilisateur')->latest()->take(10)->get();
                    @endphp
                    @forelse($recentLogs as $log)
                        <div class="flex items-start gap-3 p-3 rounded-lg transition-colors hover:shadow-md" style="background-color: #f9fafb;">
                            <div class="flex-shrink-0 rounded-lg flex items-center justify-center" style="width: 2.5rem; height: 2.5rem; background: linear-gradient(to bottom right, #06b6d4, #2563eb);">
                                <span style="font-size: 1.25rem;">{{ $log->icon ?? '🔔' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold" style="color: #111827;">{{ $log->title }}</div>
                                <div class="text-sm" style="color: #6b7280;">{{ Str::limit($log->message, 80) }}</div>
                                <div class="flex items-center gap-3 text-xs mt-1" style="color: #9ca3af;">
                                    <span>👤 {{ $log->utilisateur?->nom ?? 'N/A' }}</span>
                                    <span class="px-2 py-0.5 rounded-full" style="background-color: {{ $log->status === 'delivered' ? '#d1fae5' : '#fef3c7' }}; color: {{ $log->status === 'delivered' ? '#065f46' : '#92400e' }};">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                    <span>{{ $log->sent_at ? $log->sent_at->diffForHumans() : 'Non envoyée' }}</span>
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
    @elseif($activeTab === 'information')
        <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
            <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">ℹ️ Informations Système</h3>
            <p style="color: #6b7280;">Section Information - Contenu à définir</p>
        </div>
    @elseif($activeTab === 'monitoring')
        <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
            <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">📈 Monitoring</h3>
            <p style="color: #6b7280;">Section Monitoring - Contenu à définir</p>
        </div>
    @elseif($activeTab === 'utilisateurs')
        <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
            <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">👥 Utilisateurs</h3>
            <p style="color: #6b7280;">Section Utilisateurs - Contenu à définir</p>
        </div>
    @elseif($activeTab === 'faqs')
        <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
            <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">❓ FAQs</h3>
            <p style="color: #6b7280;">Section FAQs - Contenu à définir</p>
        </div>
    @elseif($activeTab === 'videos')
        <div class="rounded-xl shadow-lg p-6" style="background-color: #ffffff;">
            <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">🎥 Vidéos</h3>
            <p style="color: #6b7280;">Section Vidéos - Contenu à définir</p>
        </div>
    @endif

    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        @if($activeTab === 'dashboard')
        // Configuration des couleurs modernes
        const modernColors = {
            primary: ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f59e0b'],
            gradient: {
                blue: ['#3b82f6', '#1d4ed8'],
                purple: ['#8b5cf6', '#6d28d9'],
                pink: ['#ec4899', '#be185d'],
                green: ['#10b981', '#059669'],
            }
        };

        // Graphique de répartition par contexte
        const chartTypeData = @json($statsByContexte->pluck('total')->toArray());
        const chartTypeLabels = @json($statsByContexte->pluck('contexte')->map(fn($c) => ucfirst($c))->toArray());

        const chartTypeOptions = {
            series: chartTypeData,
            chart: {
                type: 'donut',
                height: 380,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                },
                fontFamily: 'Inter, sans-serif',
            },
            labels: chartTypeLabels,
            colors: modernColors.primary,
            legend: {
                position: 'bottom',
                fontSize: '14px',
                fontWeight: 500,
                markers: {
                    width: 12,
                    height: 12,
                    radius: 6,
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                fontSize: '16px',
                                fontWeight: 600,
                            },
                            value: {
                                fontSize: '24px',
                                fontWeight: 700,
                                formatter: function(val) {
                                    return val;
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '14px',
                                fontWeight: 600,
                                color: '#6b7280',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(1) + '%'
                },
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                }
            },
            stroke: {
                width: 3,
                colors: ['#fff']
            },
        };

        const chartType = new ApexCharts(document.querySelector("#chartTypeRepartition"), chartTypeOptions);
        chartType.render();

        // Graphique des scores moyens
        const scoresData = @json($scoresByType->pluck('avg_score')->map(fn($s) => round($s, 2))->toArray());
        const scoresLabels = @json($scoresByType->pluck('contexte')->map(fn($c) => ucfirst($c))->toArray());

        const chartScoresOptions = {
            series: [{
                name: 'Score moyen',
                data: scoresData
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true,
                },
                fontFamily: 'Inter, sans-serif',
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    horizontal: false,
                    columnWidth: '60%',
                    dataLabels: {
                        position: 'top',
                    },
                    distributed: true,
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(2);
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                    colors: ["#6b7280"]
                }
            },
            colors: modernColors.primary,
            xaxis: {
                categories: scoresLabels,
            },
            yaxis: {
                max: 5,
                title: {
                    text: 'Score (sur 5)',
                }
            },
            legend: {
                show: false
            }
        };

        const chartScores = new ApexCharts(document.querySelector("#chartScoresMoyens"), chartScoresOptions);
        chartScores.render();

        // Graphique d'évolution
        const evolutionDates = @json($evolution->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray());
        const evolutionData = @json($evolution->pluck('total')->toArray());
        const evolutionScores = @json($evolution->pluck('avg_score')->map(fn($s) => $s ? round($s, 2) : 0)->toArray());

        const chartEvolutionOptions = {
            series: [
                {
                    name: "Nombre d'évaluations",
                    type: 'column',
                    data: evolutionData
                },
                {
                    name: 'Score moyen',
                    type: 'line',
                    data: evolutionScores
                }
            ],
            chart: {
                height: 350,
                type: 'line',
                fontFamily: 'Inter, sans-serif',
            },
            stroke: {
                width: [0, 4],
                curve: 'smooth'
            },
            colors: ['#3b82f6', '#10b981'],
            xaxis: {
                categories: evolutionDates,
            },
            yaxis: [
                {
                    title: {
                        text: "Nombre d'évaluations",
                    },
                },
                {
                    opposite: true,
                    max: 5,
                    title: {
                        text: 'Score moyen',
                    },
                }
            ],
            legend: {
                position: 'top',
            },
        };

        const chartEvolution = new ApexCharts(document.querySelector("#chartEvolution"), chartEvolutionOptions);
        chartEvolution.render();
        @endif
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        .apexcharts-tooltip {
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .tab-link {
            cursor: pointer;
        }
    </style>
</x-filament::page>
