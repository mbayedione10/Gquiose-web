@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('type-structures.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.type_structures.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.type_structures.inputs.name')</h5>
                    <span>{{ $typeStructure->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.type_structures.inputs.icon')</h5>
                    <span>{{ $typeStructure->icon ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.type_structures.inputs.status')</h5>
                    <span>{{ $typeStructure->status ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a
                    href="{{ route('type-structures.index') }}"
                    class="btn btn-light"
                >
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\TypeStructure::class)
                <a
                    href="{{ route('type-structures.create') }}"
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
