
<x-filament::page>
    <div class="space-y-6">
        @if($record)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">{{ $record->question }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded">
                        <div class="text-sm text-gray-600">Type de formulaire</div>
                        <div class="text-lg font-semibold">
                            {{ [
                                'generale' => 'Générale',
                                'satisfaction_quiz' => 'Quiz',
                                'satisfaction_article' => 'Article',
                                'satisfaction_structure' => 'Structure',
                            ][$record->formulaire_type] ?? $record->formulaire_type }}
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded">
                        <div class="text-sm text-gray-600">Total réponses</div>
                        <div class="text-lg font-semibold">{{ $record->reponsesEvaluations->count() }}</div>
                    </div>
                    
                    <div class="bg-yellow-50 p-4 rounded">
                        <div class="text-sm text-gray-600">Type de question</div>
                        <div class="text-lg font-semibold">
                            {{ [
                                'text' => 'Texte',
                                'rating' => 'Étoiles',
                                'yesno' => 'Oui/Non',
                                'multiple_choice' => 'Choix multiples',
                                'scale' => 'Échelle',
                            ][$record->type] ?? $record->type }}
                        </div>
                    </div>
                </div>

                @if(in_array($record->type, ['rating', 'scale']))
                    <div class="bg-purple-50 p-4 rounded mb-6">
                        <div class="text-sm text-gray-600">Moyenne</div>
                        <div class="text-2xl font-bold text-purple-600">
                            {{ number_format($record->reponsesEvaluations->avg('valeur_numerique') ?? 0, 2) }}
                            / {{ $record->type === 'rating' ? '5' : '10' }}
                        </div>
                    </div>
                @endif

                @if($record->type === 'text' && $record->reponsesEvaluations->isNotEmpty())
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3">Exemples de réponses</h3>
                        <div class="space-y-2">
                            @foreach($record->reponsesEvaluations->take(10) as $reponse)
                                <div class="bg-gray-50 p-3 rounded border-l-4 border-blue-500">
                                    <p class="text-sm">{{ $reponse->reponse }}</p>
                                    <span class="text-xs text-gray-500">
                                        {{ $reponse->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-filament::page>
