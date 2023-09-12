@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('responses.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.responses.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.responses.inputs.question_id')</h5>
                    <span
                        >{{ optional($response->question)->name ?? '-' }}</span
                    >
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.responses.inputs.reponse')</h5>
                    <span>{{ $response->reponse ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.responses.inputs.isValid')</h5>
                    <span>{{ $response->isValid ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.responses.inputs.utilisateur_id')</h5>
                    <span
                        >{{ optional($response->utilisateur)->nom ?? '-'
                        }}</span
                    >
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('responses.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Response::class)
                <a href="{{ route('responses.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
