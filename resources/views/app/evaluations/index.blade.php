
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Mes Évaluations</h4>
                <a href="{{ route('evaluations.create') }}" class="btn btn-primary">
                    <i class="icon ion-md-add"></i>
                    Nouvelle évaluation
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Contexte</th>
                            <th>Score Global</th>
                            <th>Commentaire</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluations as $evaluation)
                            <tr>
                                <td>{{ $evaluation->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $evaluation->contexte_nom }}
                                    </span>
                                </td>
                                <td>
                                    @if($evaluation->score_global)
                                        <strong>{{ number_format($evaluation->score_global, 1) }}/5</strong>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($evaluation->commentaire, 50) }}</td>
                                <td>
                                    <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-sm btn-info">
                                        <i class="icon ion-md-eye"></i>
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Aucune évaluation trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $evaluations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
