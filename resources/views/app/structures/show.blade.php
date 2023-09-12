@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('structures.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.structures.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.structures.inputs.name')</h5>
                    <span>{{ $structure->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.structures.inputs.description')</h5>
                    <span>{{ $structure->description ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.structures.inputs.latitude')</h5>
                    <span>{{ $structure->latitude ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.structures.inputs.longitude')</h5>
                    <span>{{ $structure->longitude ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.structures.inputs.phone')</h5>
                    <span>{{ $structure->phone ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.structures.inputs.type_structure_id')</h5>
                    <span
                        >{{ optional($structure->typeStructure)->name ?? '-'
                        }}</span
                    >
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.structures.inputs.status')</h5>
                    <span>{{ $structure->status ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.structures.inputs.ville_id')</h5>
                    <span>{{ optional($structure->ville)->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.structures.inputs.adresse')</h5>
                    <span>{{ $structure->adresse ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('structures.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Structure::class)
                <a
                    href="{{ route('structures.create') }}"
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
