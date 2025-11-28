
<x-filament::page>
    <div class="space-y-6">
        <!-- Global Stats Widget sera affiché ici automatiquement -->
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Statistiques par type de formulaire -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Évaluations par type de formulaire</h3>
                @php
                    $stats = \App\Models\Evaluation::selectRaw('contexte, COUNT(*) as total')
                        ->groupBy('contexte')
                        ->get();
                @endphp
                <div class="space-y-3">
                    @foreach($stats as $stat)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">
                                {{ ucfirst($stat->contexte) }}
                            </span>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $stat->total }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Questions les plus répondues -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Questions les plus répondues</h3>
                @php
                    $topQuestions = \App\Models\QuestionEvaluation::withCount('reponsesEvaluations')
                        ->orderBy('reponses_evaluations_count', 'desc')
                        ->take(5)
                        ->get();
                @endphp
                <div class="space-y-3">
                    @foreach($topQuestions as $question)
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-sm font-medium line-clamp-2">
                                    {{ $question->question }}
                                </span>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold ml-2">
                                    {{ $question->reponses_evaluations_count }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $question->formulaire_type }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Scores moyens par type -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Scores moyens par type de formulaire</h3>
            @php
                $scoresByType = \App\Models\Evaluation::selectRaw('contexte, AVG(score_global) as avg_score, COUNT(*) as total')
                    ->whereNotNull('score_global')
                    ->groupBy('contexte')
                    ->get();
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($scoresByType as $score)
                    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600 mb-1">{{ ucfirst($score->contexte) }}</div>
                        <div class="text-3xl font-bold text-purple-600">
                            {{ number_format($score->avg_score, 2) }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">/ 5.00</div>
                        <div class="text-xs text-gray-400 mt-2">{{ $score->total }} évaluations</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Évolution dans le temps -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Évolution des évaluations (30 derniers jours)</h3>
            @php
                $evolution = \App\Models\Evaluation::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            @endphp
            <div class="space-y-2">
                @foreach($evolution as $day)
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600 w-28">
                            {{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}
                        </span>
                        <div class="flex-1">
                            <div class="bg-blue-200 h-6 rounded" style="width: {{ ($day->total / $evolution->max('total')) * 100 }}%">
                                <span class="text-xs text-blue-900 px-2 leading-6">{{ $day->total }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Question Chart Widget sera affiché ici automatiquement -->
    </div>
</x-filament::page>
