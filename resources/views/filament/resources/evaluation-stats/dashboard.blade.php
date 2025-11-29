
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
            ->take(5)
            ->get();

        // Distribution des alertes par type
        $alertesParType = \App\Models\Alerte::selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->get();
    @endphp

    <div class="space-y-6">
        <!-- En-tête Hero avec gradient moderne -->
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-2xl shadow-2xl p-8">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-4xl font-bold text-black mb-2 flex items-center gap-3">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Tableau de Bord - Statistiques Avancées
                        </h1>
                        <p class="text-black text-opacity-90 text-lg">
                            Période: {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
                            @if($contexte !== 'all')
                                | Filtre: <span class="font-semibold bg-white bg-opacity-20 px-3 py-1 rounded-full">{{ ucfirst($contexte) }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <div class="text-6xl font-bold text-black">{{ $evaluations->count() }}</div>
                        <div class="text-black text-opacity-90 text-lg">Évaluations</div>
                    </div>
                </div>
            </div>

            <!-- Motif décoratif -->
            <div class="absolute -bottom-6 -right-6 opacity-10">
                <svg width="200" height="200" viewBox="0 0 200 200" fill="white">
                    <circle cx="100" cy="100" r="80"/>
                </svg>
            </div>
        </div>

        <!-- Métriques clés - Grid moderne -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Utilisateurs -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-6 text-black">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold">{{ number_format($totalUtilisateurs) }}</div>
                        </div>
                    </div>
                    <div class="text-sm opacity-90">Utilisateurs inscrits</div>
                    <div class="mt-2 text-xs  bg-opacity-20 px-3 py-1 rounded-full inline-block">
                        {{ $utilisateursActifs }} actifs
                    </div>
                </div>
            </div>

            <!-- Alertes -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-6 text-black">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14  bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold">{{ number_format($totalAlertes) }}</div>
                        </div>
                    </div>
                    <div class="text-sm opacity-90">Alertes signalées</div>
                    <div class="mt-2 text-xs  bg-opacity-20 px-3 py-1 rounded-full inline-block">
                        {{ $alertesConfirmees }} confirmées
                    </div>
                </div>
            </div>

            <!-- Contenus -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-6 text-black">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14  bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold">{{ number_format($totalArticles + $totalVideos) }}</div>
                        </div>
                    </div>
                    <div class="text-sm opacity-90">Contenus publiés</div>
                    <div class="mt-2 text-xs  bg-opacity-20 px-3 py-1 rounded-full inline-block">
                        {{ $totalArticles }} articles • {{ $totalVideos }} vidéos
                    </div>
                </div>
            </div>

            <!-- Structures & Services -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-6 text-black">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14  bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold">{{ number_format($totalStructures) }}</div>
                        </div>
                    </div>
                    <div class="text-sm opacity-90">Structures d'aide</div>
                    <div class="mt-2 text-xs  bg-opacity-20 px-3 py-1 rounded-full inline-block">
                        Centres de santé
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Évaluations et Quiz -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Graphique - Répartition par contexte -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            </svg>
                        </div>
                        Répartition par contexte
                    </h3>
                </div>
                <div id="chartTypeRepartition"></div>
            </div>

            <!-- Top Questions -->
            <div class=" rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-teal-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        Questions les plus répondues
                    </h3>
                </div>
                <div class="space-y-3">
                    @foreach($topQuestions as $index => $question)
                        <div class="group flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-gray-50 to-white hover:from-green-50 hover:to-teal-50 transition-all border border-gray-100 hover:border-green-200">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-600 text-black flex items-center justify-center font-bold shadow-lg">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-green-600 transition-colors">
                                    {{ $question->question }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $question->formulaire_type }}
                                </p>
                            </div>
                            <span class="flex-shrink-0 bg-gradient-to-r from-green-100 to-teal-100 text-green-800 px-4 py-2 rounded-full text-sm font-bold shadow-sm">
                                {{ $question->reponses_evaluations_count }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Alertes récentes et Notifications -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Alertes récentes -->
            <div class="rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        Alertes récentes
                    </h3>
                </div>
                <div class="space-y-3">
                    @forelse($alertesRecentes as $alerte)
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-gradient-to-r from-gray-50 to-white hover:from-red-50 hover:to-orange-50 transition-all border border-gray-100">
                            <div class="flex-shrink-0">
                                @if($alerte->etat === 'Confirmée')
                                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                @elseif($alerte->etat === 'Rejetée')
                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                @else
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $alerte->type ?? 'Alerte' }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ Str::limit($alerte->description, 60) }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-xs text-gray-500">{{ $alerte->ville->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-500">{{ $alerte->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $alerte->etat === 'Confirmée' ? 'bg-green-100 text-green-800' : ($alerte->etat === 'Rejetée' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $alerte->etat }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Aucune alerte récente</p>
                    @endforelse
                </div>
            </div>

            <!-- Notifications push récentes -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        Notifications récentes
                    </h3>
                </div>
                <div class="space-y-3">
                    @forelse($notificationsRecentes as $notif)
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-gradient-to-r from-gray-50 to-white hover:from-indigo-50 hover:to-purple-50 transition-all border border-gray-100">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-lg">📬</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $notif->title }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ Str::limit($notif->body, 60) }}</p>
                                <span class="text-xs text-gray-500 mt-2 inline-block">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Aucune notification récente</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Scores moyens -->
        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    Scores moyens par contexte
                </h3>
            </div>
            <div id="chartScoresMoyens"></div>
        </div>

        <!-- Évolution dans le temps -->
        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    Évolution des évaluations
                </h3>
            </div>
            <div id="chartEvolution"></div>
        </div>
    </div>

    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
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

        // Graphique de répartition par contexte (Donut moderne)
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
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        const chartType = new ApexCharts(document.querySelector("#chartTypeRepartition"), chartTypeOptions);
        chartType.render();

        // Graphique des scores moyens (Bar moderne)
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
                    tools: {
                        download: true,
                    }
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
                labels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 500,
                    }
                }
            },
            yaxis: {
                max: 5,
                title: {
                    text: 'Score (sur 5)',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600,
                    }
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4,
            },
            legend: {
                show: false
            }
        };

        const chartScores = new ApexCharts(document.querySelector("#chartScoresMoyens"), chartScoresOptions);
        chartScores.render();

        // Graphique d'évolution (Area moderne)
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
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                    }
                },
                zoom: {
                    enabled: true
                },
                fontFamily: 'Inter, sans-serif',
            },
            stroke: {
                width: [0, 4],
                curve: 'smooth'
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#3b82f6', '#10b981'],
            fill: {
                type: ['solid', 'gradient'],
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.4,
                    inverseColors: false,
                    opacityFrom: 0.8,
                    opacityTo: 0.2,
                }
            },
            xaxis: {
                categories: evolutionDates,
                labels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 500,
                    }
                }
            },
            yaxis: [
                {
                    title: {
                        text: "Nombre d'évaluations",
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                        }
                    },
                    labels: {
                        style: {
                            fontSize: '12px',
                            fontWeight: 500,
                        }
                    }
                },
                {
                    opposite: true,
                    max: 5,
                    title: {
                        text: 'Score moyen',
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                        }
                    },
                    labels: {
                        style: {
                            fontSize: '12px',
                            fontWeight: 500,
                        }
                    }
                }
            ],
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                fontSize: '14px',
                fontWeight: 500,
                markers: {
                    width: 12,
                    height: 12,
                    radius: 6,
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4,
            }
        };

        const chartEvolution = new ApexCharts(document.querySelector("#chartEvolution"), chartEvolutionOptions);
        chartEvolution.render();
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        .apexcharts-tooltip {
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
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
