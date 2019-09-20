@extends('IpsumAdmin::layouts.app')
@section('title', 'Medias')

@section('content')

    <h1 class="main-title">Media</h1>

    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Ajouter</h2>
        </div>
        <div class="box-body">
            <div class="upload"
                 data-uploadendpoint="{{ route('admin.media.store') }}"
                 data-uploadmedias="{{ route('admin.media.new') }}"
                 data-uploadrepertoire=""
                 data-uploadpublicationid=""
                 data-uploadpublicationtype=""
                 data-uploadgroupe=""
                 data-uploadnote="Images et documents, poids maximum {{ config('ipsum.media.upload_max_filesize') }} Ko"
                 data-uploadmaxfilesize="{{ config('ipsum.media.upload_max_filesize') }}"
                 data-uploadmmaxnumberoffiles=""
                 data-uploadminnumberoffiles=""
                 data-uploadallowedfiletypes=""
                 data-uploadcsrftoken="{{ csrf_token() }}">
                <div class="upload-DragDrop"></div>
                <div class="upload-ProgressBar"></div>
                <div class="upload-alerts mt-3"></div>
                <div class="mt-3">
                    <h3>Nouveaux médias :</h3>
                    <div class="d-flex flex-row flex-wrap upload-files">
                    </div>
                </div>
            </div>
            <link href="{{ asset('ipsum/admin/dist/uppy.css') }}" rel="stylesheet">
            <script src="{{ asset('ipsum/admin/dist/uppy.js') }}"></script>
        </div>
    </div>

@endsection