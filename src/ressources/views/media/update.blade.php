@extends('IpsumAdmin::layouts.app')
@section('title', 'Medias')

@section('content')

    <h1 class="main-title">Média</h1>

    {{ Aire::open()->route('admin.media.update', [$media->id, request()->route('locale')])->bind($media)->formRequest(\Ipsum\Media\app\Http\Requests\StoreMedia::class) }}
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <div class="box-toolbar"><a href="{{ asset($media->path) }}">Voir le média</a></div>
                </div>
                <div class="box-body text-center">
                    @if ($media->isImage)
                        <img class="img-fluid" src="{{ Croppa::url($media->cropPath, 700) }}" alt="{{ $media->tagAlt }}" />
                    @else
                        <span class="media-icone {{ $media->icone }} pb-4">{{ $media->type }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Détail du média</h3>
                    <div class="btn-toolbar">
                        @if ($media->exists and count(config('ipsum.translate.locales')) > 1)
                            <ul class="nav nav-tabs" role="tablist">
                                @foreach(config('ipsum.translate.locales') as $locale)
                                    <li class="nav-item">
                                        <a class="nav-link {{ (request()->route('locale') == $locale['nom'] or (request()->route('locale') === null and config('ipsum.translate.default_locale') == $locale['nom'])) ? 'active' : '' }}" href="{{ route('admin.media.edit', [$media, $locale['nom']]) }}" aria-selected="true">{{ $locale['intitule'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <div class="box-body">
                    {{ Aire::input('titre', 'Titre*') }}
                    {{ Aire::input('alt', 'Texte alternatif') }}
                    {{ Aire::textArea('description', 'Description')->class('tinymce-simple') }}
                    <script src="{{ asset('ipsum/admin/dist/tinymce.js') }}"></script>
                    {{ Aire::input('url', 'Lien') }}
                </div>
                <div class="box-footer">
                    <div><button class="btn btn-outline-secondary" type="reset">Annuler</button></div>
                    <div><button class="btn btn-primary" type="submit">Enregistrer</button></div>
                </div>
            </div>
        </div>
    </div>
    {{ Aire::close() }}

@endsection