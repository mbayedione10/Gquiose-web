
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('evaluations.index') }}" class="mr-4">
                    <i class="icon ion-md-arrow-back"></i>
                </a>
                Formulaire d'Évaluation
            </h4>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('evaluations.submit') }}">
                @csrf

                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                <input type="hidden" name="contexte" value="{{ request('contexte', 'generale') }}">
                <input type="hidden" name="contexte_id" value="{{ request('contexte_id') }}">

                @foreach($questions as $question)
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">
                            {{ $question->question }}
                            @if($question->obligatoire)
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        @if($question->type === 'text')
                            <textarea 
                                name="reponses[{{ $loop->index }}][reponse]" 
                                class="form-control @error("reponses.{$loop->index}.reponse") is-invalid @enderror"
                                rows="3"
                                {{ $question->obligatoire ? 'required' : '' }}
                            >{{ old("reponses.{$loop->index}.reponse") }}</textarea>

                        @elseif($question->type === 'rating')
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="star-label">
                                        <input 
                                            type="radio" 
                                            name="reponses[{{ $loop->index }}][valeur_numerique]" 
                                            value="{{ $i }}"
                                            {{ $question->obligatoire ? 'required' : '' }}
                                            {{ old("reponses.{$loop->index}.valeur_numerique") == $i ? 'checked' : '' }}
                                            onchange="document.querySelector('input[name=\'reponses[{{ $loop->index }}][reponse]\']').value = this.value + '/5'"
                                        >
                                        <i class="icon ion-md-star"></i>
                                    </label>
                                @endfor
                            </div>
                            <input type="hidden" name="reponses[{{ $loop->index }}][reponse]" value="{{ old("reponses.{$loop->index}.reponse") }}">

                        @elseif($question->type === 'scale')
                            <div class="d-flex align-items-center">
                                <span class="mr-3">1</span>
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="mx-2">
                                        <input 
                                            type="radio" 
                                            name="reponses[{{ $loop->index }}][valeur_numerique]" 
                                            value="{{ $i }}"
                                            {{ $question->obligatoire ? 'required' : '' }}
                                            {{ old("reponses.{$loop->index}.valeur_numerique") == $i ? 'checked' : '' }}
                                            onchange="document.querySelector('input[name=\'reponses[{{ $loop->index }}][reponse]\']').value = this.value + '/5'"
                                        >
                                        {{ $i }}
                                    </label>
                                @endfor
                                <span class="ml-3">5</span>
                            </div>
                            <input type="hidden" name="reponses[{{ $loop->index }}][reponse]" value="{{ old("reponses.{$loop->index}.reponse") }}">

                        @elseif($question->type === 'yesno')
                            <div>
                                <label class="mr-3">
                                    <input 
                                        type="radio" 
                                        name="reponses[{{ $loop->index }}][reponse]" 
                                        value="Oui"
                                        {{ $question->obligatoire ? 'required' : '' }}
                                        {{ old("reponses.{$loop->index}.reponse") == 'Oui' ? 'checked' : '' }}
                                    >
                                    Oui
                                </label>
                                <label>
                                    <input 
                                        type="radio" 
                                        name="reponses[{{ $loop->index }}][reponse]" 
                                        value="Non"
                                        {{ $question->obligatoire ? 'required' : '' }}
                                        {{ old("reponses.{$loop->index}.reponse") == 'Non' ? 'checked' : '' }}
                                    >
                                    Non
                                </label>
                            </div>

                        @elseif($question->type === 'multiple_choice' && $question->options)
                            @foreach($question->options as $option)
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="reponses[{{ $loop->parent->index }}][reponse]" 
                                        value="{{ $option }}"
                                        id="question_{{ $loop->parent->index }}_option_{{ $loop->index }}"
                                        {{ $question->obligatoire ? 'required' : '' }}
                                        {{ old("reponses.{$loop->parent->index}.reponse") == $option ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="question_{{ $loop->parent->index }}_option_{{ $loop->index }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        @endif

                        <input type="hidden" name="reponses[{{ $loop->index }}][question_id]" value="{{ $question->id }}">

                        @error("reponses.{$loop->index}.reponse")
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach

                <div class="form-group">
                    <label for="commentaire">Commentaire additionnel (optionnel)</label>
                    <textarea 
                        name="commentaire" 
                        id="commentaire" 
                        class="form-control @error('commentaire') is-invalid @enderror"
                        rows="4"
                        maxlength="1000"
                    >{{ old('commentaire') }}</textarea>
                    @error('commentaire')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon ion-md-send"></i>
                        Soumettre l'évaluation
                    </button>
                    <a href="{{ route('evaluations.index') }}" class="btn btn-light">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.rating-stars {
    display: flex;
    gap: 10px;
}

.star-label {
    cursor: pointer;
    font-size: 30px;
    color: #ddd;
    transition: color 0.2s;
}

.star-label input {
    display: none;
}

.star-label:hover,
.star-label:hover ~ .star-label {
    color: #ffc107;
}

.star-label input:checked ~ i,
.star-label:has(input:checked) i {
    color: #ffc107;
}
</style>
@endsection
