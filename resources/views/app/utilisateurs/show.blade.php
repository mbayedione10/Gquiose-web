@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('utilisateurs.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.utilisateurs.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.utilisateurs.inputs.nom')</h5>
                    <span>{{ $utilisateur->nom ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.utilisateurs.inputs.prenom')</h5>
                    <span>{{ $utilisateur->prenom ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.utilisateurs.inputs.email')</h5>
                    <span>{{ $utilisateur->email ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.utilisateurs.inputs.phone')</h5>
                    <span>{{ $utilisateur->phone ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.utilisateurs.inputs.sexe')</h5>
                    <span>{{ $utilisateur->sexe ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.utilisateurs.inputs.status')</h5>
                    <span>{{ $utilisateur->status ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a
                    href="{{ route('utilisateurs.index') }}"
                    class="btn btn-light"
                >
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Utilisateur::class)
                <a
                    href="{{ route('utilisateurs.create') }}"
                    class="btn btn-light"
                >
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
