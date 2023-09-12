@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('alertes.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.alertes.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.alertes.inputs.ref')</h5>
                    <span>{{ $alerte->ref ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.alertes.inputs.description')</h5>
                    <span>{{ $alerte->description ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.alertes.inputs.latitude')</h5>
                    <span>{{ $alerte->latitude ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.alertes.inputs.longitude')</h5>
                    <span>{{ $alerte->longitude ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.alertes.inputs.type_alerte_id')</h5>
                    <span
                        >{{ optional($alerte->typeAlerte)->name ?? '-' }}</span
                    >
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.alertes.inputs.etat')</h5>
                    <span>{{ $alerte->etat ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.alertes.inputs.ville_id')</h5>
                    <span>{{ optional($alerte->ville)->name ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('alertes.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Alerte::class)
                <a href="{{ route('alertes.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
