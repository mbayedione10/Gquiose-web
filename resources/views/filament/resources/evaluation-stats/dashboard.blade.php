
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
    @endphp

    <div class="space-y-6">
        <!-- Période de filtrage affichée -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-6 text-black">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Tableau de bord des statistiques</h2>
                    <p class="text-blue-100">
                        Période: {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
                        @if($contexte !== 'all')
                            | Filtre: <span class="font-semibold">{{ ucfirst($contexte) }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-bold">{{ $evaluations->count() }}</div>
                    <div class="text-sm text-blue-100">Évaluations</div>
                </div>
            </div>
        </div>

        <!-- Global Stats Widget -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Graphique en camembert - Répartition par type -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    Répartition par contexte
                </h3>
                <div id="chartTypeRepartition"></div>
            </div>

            <!-- Questions les plus répondues -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Top 5 Questions
                </h3>
                <div class="space-y-3">
                    @foreach($topQuestions as $index => $question)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-gradient-to-r from-gray-50 to-white hover:from-blue-50 hover:to-purple-50 transition-all">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-white flex items-center justify-center font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $question->question }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $question->formulaire_type }}
                                </p>
                            </div>
                            <span class="flex-shrink-0 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">
                                {{ $question->reponses_evaluations_count }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Scores moyens par contexte -->
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                Scores moyens par contexte
            </h3>
            <div id="chartScoresMoyens"></div>
        </div>

        <!-- Évolution dans le temps -->
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                Évolution des évaluations
            </h3>
            <div id="chartEvolution"></div>
        </div>
    </div>

    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // Graphique de répartition par contexte (Donut)
        const chartTypeData = @json($statsByContexte->pluck('total')->toArray());
        const chartTypeLabels = @json($statsByContexte->pluck('contexte')->map(fn($c) => ucfirst($c))->toArray());

        const chartTypeOptions = {
            series: chartTypeData,
            chart: {
                type: 'donut',
                height: 350,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                }
            },
            labels: chartTypeLabels,
            colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '22px',
                                fontWeight: 600,
                                color: '#374151'
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(1) + '%'
                }
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

        // Graphique des scores moyens (Bar)
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
                    show: true
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    horizontal: false,
                    columnWidth: '60%',
                    dataLabels: {
                        position: 'top',
                    },
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
                    colors: ["#304758"]
                }
            },
            colors: ['#8b5cf6'],
            xaxis: {
                categories: scoresLabels,
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                max: 5,
                title: {
                    text: 'Score (sur 5)'
                }
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            }
        };

        const chartScores = new ApexCharts(document.querySelector("#chartScoresMoyens"), chartScoresOptions);
        chartScores.render();

        // Graphique d'évolution (Area)
        const evolutionDates = @json($evolution->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray());
        const evolutionData = @json($evolution->pluck('total')->toArray());
        const evolutionScores = @json($evolution->pluck('avg_score')->map(fn($s) => $s ? round($s, 2) : 0)->toArray());

        const chartEvolutionOptions = {
            series: [
                {
                    name: 'Nombre d\'évaluations',
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
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            stroke: {
                width: [0, 4],
                curve: 'smooth'
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#3b82f6', '#10b981'],
            xaxis: {
                categories: evolutionDates
            },
            yaxis: [
                {
                    title: {
                        text: 'Nombre d\'évaluations',
                    },
                },
                {
                    opposite: true,
                    max: 5,
                    title: {
                        text: 'Score moyen'
                    }
                }
            ],
            legend: {
                position: 'top'
            },
            grid: {
                borderColor: '#e7e7e7'
            }
        };

        const chartEvolution = new ApexCharts(document.querySelector("#chartEvolution"), chartEvolutionOptions);
        chartEvolution.render();
    </script>
</x-filament::page>
