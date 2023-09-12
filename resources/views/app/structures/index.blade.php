@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between;">
                <h4 class="card-title">@lang('crud.structures.index_title')</h4>
            </div>

            <div class="searchbar mt-4 mb-5">
                <div class="row">
                    <div class="col-md-6">
                        <form>
                            <div class="input-group">
                                <input
                                    id="indexSearch"
                                    type="text"
                                    name="search"
                                    placeholder="{{ __('crud.common.search') }}"
                                    value="{{ $search ?? '' }}"
                                    class="form-control"
                                    autocomplete="off"
                                />
                                <div class="input-group-append">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        <i class="icon ion-md-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-right">
                        @can('create', App\Models\Structure::class)
                        <a
                            href="{{ route('structures.create') }}"
                            class="btn btn-primary"
                        >
                            <i class="icon ion-md-add"></i>
                            @lang('crud.common.create')
                        </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless table-hover">
                    <thead>
                        <tr>
                            <th class="text-left">
                                @lang('crud.structures.inputs.name')
                            </th>
                            <th class="text-left">
                                @lang('crud.structures.inputs.description')
                            </th>
                            <th class="text-right">
                                @lang('crud.structures.inputs.latitude')
                            </th>
                            <th class="text-right">
                                @lang('crud.structures.inputs.longitude')
                            </th>
                            <th class="text-left">
                                @lang('crud.structures.inputs.phone')
                            </th>
                            <th class="text-left">
                                @lang('crud.structures.inputs.type_structure_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.structures.inputs.status')
                            </th>
                            <th class="text-left">
                                @lang('crud.structures.inputs.ville_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.structures.inputs.adresse')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($structures as $structure)
                        <tr>
                            <td>{{ $structure->name ?? '-' }}</td>
                            <td>{{ $structure->description ?? '-' }}</td>
                            <td>{{ $structure->latitude ?? '-' }}</td>
                            <td>{{ $structure->longitude ?? '-' }}</td>
                            <td>{{ $structure->phone ?? '-' }}</td>
                            <td>
                                {{ optional($structure->typeStructure)->name ??
                                '-' }}
                            </td>
                            <td>{{ $structure->status ?? '-' }}</td>
                            <td>
                                {{ optional($structure->ville)->name ?? '-' }}
                            </td>
                            <td>{{ $structure->adresse ?? '-' }}</td>
                            <td class="text-center" style="width: 134px;">
                                <div
                                    role="group"
                                    aria-label="Row Actions"
                                    class="btn-group"
                                >
                                    @can('update', $structure)
                                    <a
                                        href="{{ route('structures.edit', $structure) }}"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                        >
                                            <i class="icon ion-md-create"></i>
                                        </button>
                                    </a>
                                    @endcan @can('view', $structure)
                                    <a
                                        href="{{ route('structures.show', $structure) }}"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                        >
                                            <i class="icon ion-md-eye"></i>
                                        </button>
                                    </a>
                                    @endcan @can('delete', $structure)
                                    <form
                                        action="{{ route('structures.destroy', $structure) }}"
                                        method="POST"
                                        onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')"
                                    >
                                        @csrf @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-light text-danger"
                                        >
                                            <i class="icon ion-md-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">
                                @lang('crud.common.no_items_found')
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="10">{!! $structures->render() !!}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
