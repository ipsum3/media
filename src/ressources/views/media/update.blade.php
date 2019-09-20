@extends('IpsumAdmin::layouts.app')
@section('title', 'Medias')

@section('content')

    <h1 class="main-title">Média</h1>

    {{ Aire::open()->route('admin.media.update', $media->id)->bind($media)->formRequest(\Ipsum\Media\app\Http\Requests\StoreMedia::class) }}
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <div class="box-toolbar"><a href="{{ asset($media->path) }}">Voir le média</a></div>
                </div>
                @if ($media->isImage)
                    <img class="img-fluid" src="{{ Croppa::url($media->cropPath, 700) }}" alt="{{ $media->tagAlt }}" />
                @else
                    <span class="media-icone {{ $media->icone }} pb-4">{{ $media->type }}</span>
                @endif
                <div></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Détail du média</h3>
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