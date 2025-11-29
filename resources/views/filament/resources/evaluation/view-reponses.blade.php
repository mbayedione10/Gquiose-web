@php
    $evaluation = $this->record ?? $getRecord();
    $reponsesEvaluations = $evaluation->reponsesEvaluations()->with('questionEvaluation')->get();
@endphp

<div class="space-y-4">
    @if($reponsesEvaluations->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">
                    Réponses de l'évaluation ({{ $reponsesEvaluations->count() }} questions)
                </h3>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($reponsesEvaluations as $index => $reponse)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-4">
                            <!-- Numéro de la question -->
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-white flex items-center justify-center font-bold text-lg">
                                {{ $index + 1 }}
                            </div>

                            <!-- Contenu de la question et réponse -->
                            <div class="flex-1 min-w-0">
                                <!-- Question -->
                                <div class="mb-3">
                                    <p class="text-base font-semibold text-gray-900 mb-1">
                                        {{ $reponse->questionEvaluation->question }}
                                    </p>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($reponse->questionEvaluation->type) }}
                                        </span>
                                        @if($reponse->questionEvaluation->obligatoire)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Obligatoire
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Réponse -->
                                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-500">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600 mb-1 font-medium">Réponse:</p>
                                            <p class="text-base text-gray-900">
                                                {{ $reponse->reponse }}
                                            </p>
                                        </div>

                                        <!-- Score si disponible -->
                                        @if($reponse->valeur_numerique !== null)
                                            <div class="ml-4 flex-shrink-0">
                                                @php
                                                    $question = $reponse->questionEvaluation;
                                                    $valeur = $reponse->valeur_numerique;

                                                    // Calculer le score normalisé sur 5
                                                    if ($question->type === 'scale') {
                                                        $options = is_array($question->options) ? $question->options : json_decode($question->options, true);
                                                        $max = $options['max'] ?? 10;
                                                        $scoreNormalise = round(($valeur / $max) * 5, 2);
                                                    } elseif ($question->type === 'rating') {
                                                        $scoreNormalise = $valeur;
                                                    } elseif ($question->type === 'yesno') {
                                                        $scoreNormalise = $valeur;
                                                    } else {
                                                        $scoreNormalise = null;
                                                    }
                                                @endphp

                                                @if($scoreNormalise !== null)
                                                    <div class="text-center">
                                                        <div class="text-2xl font-bold text-purple-600">
                                                            {{ number_format($scoreNormalise, 1) }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">/ 5</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Résumé -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div>
                            <p class="text-sm text-gray-600">Score global</p>
                            <p class="text-2xl font-bold text-purple-600">
                                {{ $evaluation->score_global ? number_format($evaluation->score_global, 2) : 'N/A' }}/5
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Type de formulaire</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ ucfirst($evaluation->contexte) }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Date</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ $evaluation->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commentaire si présent -->
        @if($evaluation->commentaire)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-yellow-50 px-6 py-3 border-b border-yellow-100">
                    <h4 class="text-sm font-semibold text-yellow-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        Commentaire additionnel
                    </h4>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-700 italic">
                        "{{ $evaluation->commentaire }}"
                    </p>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="mt-4 text-gray-500">Aucune réponse disponible pour cette évaluation</p>
        </div>
    @endif
</div>
