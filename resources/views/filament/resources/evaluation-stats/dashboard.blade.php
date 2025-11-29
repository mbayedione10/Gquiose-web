
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

    <!-- En-tête avec filtres -->
    <div class="mb-6 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2 text-white">
                    📊 Statistiques & Graphiques
                </h1>
            </div>
        </div>
    </div>

    <!-- Métriques clés - Grid moderne -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Évaluations -->
        <div class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-indigo-500">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($evaluations->count()) }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Évaluations</div>
                <div class="mt-2 text-xs bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-full inline-block">
                    Période sélectionnée
                </div>
            </div>
        </div>

        <!-- Score Moyen Global -->
        <div class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-green-500">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $evaluations->avg('score_global') ? number_format($evaluations->avg('score_global'), 2) : '0.00' }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Score Moyen Global</div>
                <div class="mt-2 text-xs bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 px-3 py-1 rounded-full inline-block">
                    Sur 5.00
                </div>
            </div>
        </div>

        <!-- Questions Évaluées -->
        <div class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-orange-500">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-amber-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($totalQuestions) }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Questions au Total</div>
                <div class="mt-2 text-xs bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300 px-3 py-1 rounded-full inline-block">
                    Bibliothèque complète
                </div>
            </div>
        </div>

        <!-- Cycles Menstruels -->
        <div class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-pink-500">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-pink-600 dark:text-pink-400">{{ number_format($cyclesActifs) }}</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Cycles Actifs</div>
                <div class="mt-2 text-xs bg-pink-100 dark:bg-pink-900/50 text-pink-700 dark:text-pink-300 px-3 py-1 rounded-full inline-block">
                    Suivi en cours
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques Statistiques -->
    <div class="mb-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Répartition par contexte -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">📊 Répartition par Contexte</h3>
            <div id="chartTypeRepartition"></div>
        </div>

        <!-- Scores moyens par contexte -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">⭐ Scores Moyens par Contexte</h3>
            <div id="chartScoresMoyens"></div>
        </div>
    </div>

    <!-- Graphique d'évolution -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">📈 Évolution Temporelle</h3>
            <div id="chartEvolution"></div>
        </div>
    </div>

    <!-- Questions les plus populaires -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">🔥 Top 5 Questions les Plus Évaluées</h3>
            <div class="space-y-3">
                @forelse($topQuestions as $index => $question)
                    <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $question->libelle }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $question->reponses_evaluations_count }} réponses</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        Aucune question évaluée pour la période sélectionnée
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Alertes récentes et Distribution -->
    <div class="mb-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Alertes récentes -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">🚨 5 Dernières Alertes</h3>
            <div class="space-y-3">
                @forelse($alertesRecentes as $alerte)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-rose-500 to-red-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $alerte->type }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $alerte->description }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                {{ $alerte->utilisateur?->name ?? 'Utilisateur inconnu' }} • {{ $alerte->ville?->nom ?? 'Ville inconnue' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        Aucune alerte récente
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Distribution des alertes -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">📊 Distribution des Alertes par Type</h3>
            <div class="space-y-3">
                @forelse($alertesParType as $typeAlerte)
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($typeAlerte->type) }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $typeAlerte->total }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-rose-500 to-red-600 h-2 rounded-full"
                                     style="width: {{ $totalAlertes > 0 ? ($typeAlerte->total / $totalAlertes * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        Aucune donnée disponible
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Notifications récentes -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">🔔 5 Dernières Notifications Push</h3>
            <div class="space-y-3">
                @forelse($notificationsRecentes as $notif)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $notif->title }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $notif->body }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                {{ $notif->created_at->diffForHumans() }} • Type: {{ $notif->type }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        Aucune notification récente
                    </div>
                @endforelse
            </div>
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
