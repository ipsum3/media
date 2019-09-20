@extends('IpsumAdmin::layouts.login')
@section('title', 'Médias')

@section('content')
    <div class="p-3">
        <div class="box">
            <div class="box-header">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                    <h2 class="box-title">Liste ({{ $medias->total() }})</h2>
                    {{--<div class="btn-toolbar">
                        <a class="btn btn-primary" href="{{ route('admin.media.create') }}">
                            <i class="fas fa-plus"></i>
                            Ajouter
                        </a>
                    </div>--}}
                </div>
            </div>
            <div class="box-body">

                <div class="d-flex flex-rowp justify-content-between">
                    <div class="flex-grow-1">
                        {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.media.popin') }}
                        <label class="sr-only" for="search">Recherche</label>
                        {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}

                        <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
                        {{ Aire::close() }}

                        <div class="d-flex flex-row flex-wrap">
                            @foreach ($medias as $media)
                                @include('IpsumMedia::media._media', ['sortable' => false, 'media_add' => true])
                            @endforeach
                        </div>

                        {!! $medias->appends(request()->all())->links() !!}
                    </div>
                    <div style="min-width: 220px">
                        <div class="sticky-top">
                            <div id="media-info"></div>
                            <script id="media-info-template" type="x-tmpl-mustache">
                                <div class="p-3 bg-light">
                                    <h3>@{{ titre }}</h3>
                                    <div class="text-center mb-1 mb-2">
                                        @{{#isImage}}<img src="@{{ image }}" alt="">@{{/isImage}}
                                        @{{^isImage}}<span class="@{{ icone }}"></span>@{{/isImage}}
                                    </div>
                                    @{{#isImage}}
                                    <div class="form-group">
                                        <label for="alignement">Alignement</label>
                                        <select id="alignement" name="alignement" class="form-control">
                                            @foreach (config('ipsum.media.alignements') as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="taille">Taille</label>
                                        <select id="taille" name="taille" class="form-control">
                                            @foreach (config('ipsum.media.tailles') as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @{{/isImage}}
                                    <button class="btn btn-primary" id="textarea-add" type="submit">Ajouter</button>
                                </div>
                            </script>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection
