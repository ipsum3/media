@extends('IpsumAdmin::layouts.app')
@section('title', 'Medias')

@section('content')

    <h1 class="main-title modal-title">Medias</h1>
    <div class="box">
        <div class="box-header">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h2 class="box-title">Liste ({{ $medias->total() }})</h2>
                <div class="btn-toolbar">
                    <a class="btn btn-primary" href="{{ route('admin.media.create') }}">
                        <i class="fas fa-plus"></i>
                        Ajouter
                    </a>
                </div>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.media.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <div class="d-flex flex-row flex-wrap">
                @foreach ($medias as $media)
                    @include('IpsumMedia::media._media', ['sortable' => false])
                @endforeach
            </div>

            {!! $medias->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection