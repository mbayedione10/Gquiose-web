@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('suivis.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.suivis.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.suivis.inputs.name')</h5>
                    <span>{{ $suivi->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.suivis.inputs.observation')</h5>
                    <span>{{ $suivi->observation ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.suivis.inputs.alerte_id')</h5>
                    <span>{{ optional($suivi->alerte)->ref ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('suivis.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Suivi::class)
                <a href="{{ route('suivis.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
