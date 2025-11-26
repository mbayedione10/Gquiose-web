
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('evaluations.index') }}" class="mr-4">
                    <i class="icon ion-md-arrow-back"></i>
                </a>
                Détails de l'évaluation
            </h4>

            <div class="mt-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Date :</strong> {{ $evaluation->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Contexte :</strong> 
                        <span class="badge badge-info">{{ $evaluation->contexte_nom }}</span>
                    </div>
                </div>

                @if($evaluation->score_global)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Score global :</strong> 
                            <span class="badge badge-success">{{ number_format($evaluation->score_global, 1) }}/5</span>
                        </div>
                    </div>
                @endif

                <hr>

                <h5 class="mb-3">Réponses détaillées</h5>

                @foreach($evaluation->reponsesDetails as $reponse)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">
                                {{ $reponse->questionEvaluation->question }}
                            </h6>
                            
                            @if($reponse->questionEvaluation->type === 'rating' && $reponse->valeur_numerique)
                                <div class="rating-display">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="icon ion-md-star {{ $i <= $reponse->valeur_numerique ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ml-2">({{ $reponse->valeur_numerique }}/5)</span>
                                </div>
                            @elseif($reponse->questionEvaluation->type === 'scale' && $reponse->valeur_numerique)
                                <p class="card-text">
                                    <strong>{{ $reponse->valeur_numerique }}/5</strong>
                                </p>
                            @else
                                <p class="card-text">{{ $reponse->reponse }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if($evaluation->commentaire)
                    <hr>
                    <h5 class="mb-3">Commentaire</h5>
                    <div class="card">
                        <div class="card-body">
                            {{ $evaluation->commentaire }}
                        </div>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('evaluations.index') }}" class="btn btn-secondary">
                        <i class="icon ion-md-arrow-back"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-display i {
    font-size: 24px;
}
</style>
@endsection
