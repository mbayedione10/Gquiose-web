@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between;">
                <h4 class="card-title">@lang('crud.articles.index_title')</h4>
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
                        @can('create', App\Models\Article::class)
                        <a
                            href="{{ route('articles.create') }}"
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
                                @lang('crud.articles.inputs.title')
                            </th>
                            <th class="text-left">
                                @lang('crud.articles.inputs.description')
                            </th>
                            <th class="text-left">
                                @lang('crud.articles.inputs.rubrique_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.articles.inputs.slug')
                            </th>
                            <th class="text-left">
                                @lang('crud.articles.inputs.image')
                            </th>
                            <th class="text-left">
                                @lang('crud.articles.inputs.status')
                            </th>
                            <th class="text-left">
                                @lang('crud.articles.inputs.user_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.articles.inputs.video_url')
                            </th>
                            <th class="text-left">
                                @lang('crud.articles.inputs.audio_url')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                        <tr>
                            <td>{{ $article->title ?? '-' }}</td>
                            <td>{{ $article->description ?? '-' }}</td>
                            <td>
                                {{ optional($article->rubrique)->name ?? '-' }}
                            </td>
                            <td>{{ $article->slug ?? '-' }}</td>
                            <td>
                                <x-partials.thumbnail
                                    src="{{ $article->image ? \Storage::url($article->image) : '' }}"
                                />
                            </td>
                            <td>{{ $article->status ?? '-' }}</td>
                            <td>{{ optional($article->user)->name ?? '-' }}</td>
                            <td>{{ $article->video_url ?? '-' }}</td>
                            <td>{{ $article->audio_url ?? '-' }}</td>
                            <td class="text-center" style="width: 134px;">
                                <div
                                    role="group"
                                    aria-label="Row Actions"
                                    class="btn-group"
                                >
                                    @can('update', $article)
                                    <a
                                        href="{{ route('articles.edit', $article) }}"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                        >
                                            <i class="icon ion-md-create"></i>
                                        </button>
                                    </a>
                                    @endcan @can('view', $article)
                                    <a
                                        href="{{ route('articles.show', $article) }}"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                        >
                                            <i class="icon ion-md-eye"></i>
                                        </button>
                                    </a>
                                    @endcan @can('delete', $article)
                                    <form
                                        action="{{ route('articles.destroy', $article) }}"
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
                            <td colspan="10">{!! $articles->render() !!}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
