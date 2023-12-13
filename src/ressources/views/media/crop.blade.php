@extends('IpsumAdmin::layouts.app')
@section('title', 'Medias')

@section('content')
    <!-- CDN pour Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        canvas{
            max-width: 100%;
        }
    </style>

    <h1 class="main-title">Média</h1>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-toolbar">
                        <a class="btn btn-outline-secondary" href="{{route('admin.media.edit', [$media->id, request()->route('locale')])}}">
                            <i class="fa fa-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </div>
                <div class="box-body text-center" style="height: 100vh;">
                    @csrf
                    <div class="row">
                    <div class="col col-9">
                        @if ($media->isImage)
                            {{--<img id="image" class="img-fluid" src="{{ Croppa::url($media->cropPath, 700) }}" alt="{{ $media->tagAlt }}" />--}}
                            <img id="image" class="img-fluid" src="{{ asset($media->path) }}" alt="{{ $media->tagAlt }}" />
                        @else
                            <span class="media-icone {{ $media->icone }} pb-4">{{ $media->type }}</span>
                        @endif
                    </div>
                    <div class="col col-3">
                        <p class="d-none">Data: <span id="data"></span></p>
                        <p class="d-none">Crop Box Data: <span id="cropBoxData"></span></p>
                        <input type="file" class="sr-only" id="fileInput" name="image" accept="image/*">

                        <button onclick="$('#fileInput').click()" class="btn btn-secondary mt-2" data-tooltip="tooltip" title="Import image with Blob URLs"> <span class="fa fa-upload"></span> Importer</button>

                        <div class="row justify-content-center mt-2">
                            <input class="col col-4 form-control " type="number" id="width" name="width" data-size="width" placeholder="Largeur">
                            <input class="col col-4 form-control " type="number" id="height" name="height" data-size="height" placeholder="Hauteur">
                        </div>
                        <div>
                            <!-- Boutons pour modifier le ratio de crop -->
                            <button class="btn btn-secondary mt-2" data-crop="16/9">16:9</button>
                            <button class="btn btn-secondary mt-2" data-crop="4/3">4:3</button>
                            <button class="btn btn-secondary mt-2" data-crop="1/1">1:1</button>
                            <button class="btn btn-secondary mt-2" data-crop="1/1">2:3</button>
                            <button class="btn btn-secondary mt-2" data-crop="NaN">Libre</button>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-secondary mt-2" data-crop="cropper.move(-10, 0)" title="Move Left"><span class="fa fa-arrow-left"></span></button>
                            <button class="btn btn-secondary mt-2" data-crop="cropper.move(10, 0)" title="Move Right"><span class="fa fa-arrow-right"></span></button>
                            <button class="btn btn-secondary mt-2" data-crop="cropper.move(0, -10)" title="Move Up"><span class="fa fa-arrow-up"></span></button>
                            <button class="btn btn-secondary mt-2" data-crop="cropper.move(0, 10)" title="Move Down"><span class="fa fa-arrow-down"></span></button>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-secondary mt-2" data-scale="x"  title="Flip Horizontal"><span class="fa fa-arrows-alt-h"></span></button>
                            <button class="btn btn-secondary mt-2" data-scale="y" title="Flip Vertical"><span class="fa fa-arrows-alt-v"></span></button>
                            <button class="btn btn-secondary mt-2" data-rotate="-45"  title="Rotate Left"><span class="fa fa-undo-alt"></span></button>
                            <button class="btn btn-secondary mt-2" data-rotate="45" title="Rotate Right"><span class="fa fa-redo-alt"></span></button>
                        </div>
                        <div>
                            <!-- Boutons pour zoomer et dézoomer -->
                            <button class="btn btn-secondary mt-2" data-zoom="0.1"><span class="fa fa-search-plus"></span></button>
                            <button class="btn btn-secondary mt-2" data-zoom="-0.1"><span class="fa fa-search-minus"></span></button>
                        </div>



                        <button id="cropButton" class="btn btn-primary mt-2">Enregistrer</button>
                        <button id="button" class="btn btn-primary mt-2 d-none">Crop</button>
                        <div id="result" class="m-5"></div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        window.addEventListener('DOMContentLoaded', function () {
            var image = document.querySelector('#image');
            var buttoncrop = document.getElementById('button');
            var result = document.getElementById('result');
            var cropButton = document.getElementById('cropButton');

            var cropper = new Cropper(image, {
                ready: function (event) {
                    buttoncrop.click()
                },

                crop: function (event) {
                    buttoncrop.click()
                    document.getElementById('width').value = parseInt(cropper.getCropBoxData().width)
                    document.getElementById('height').value = parseInt(cropper.getCropBoxData().height)
                },

                zoom: function (event) {
                    // Keep the image in its natural size
                    if (event.detail.oldRatio === 1) {
                        event.preventDefault();
                    }
                },
            });

            document.querySelectorAll('[data-size]').forEach(button => {
                button.addEventListener('change', () => {
                    let width = cropper.getCropBoxData().width;
                    let height = cropper.getCropBoxData().height;
                    if(parseInt(document.getElementById('width').value)){
                        width = parseInt(document.getElementById('width').value);
                    }
                    if(parseInt(document.getElementById('width').value)){
                        height = parseInt(document.getElementById('height').value);
                    }

                    cropper.setCropBoxData({
                        width: parseInt(document.getElementById('width').value),
                        height: parseInt(document.getElementById('height').value)
                    });
                });
            });

            buttoncrop.onclick = function () {
                result.innerHTML = '';
                result.appendChild(cropper.getCroppedCanvas());
            };

            cropButton.onclick = function () {
                // Récupérez les données de l'image croppée et effectuez les actions nécessaires pour les enregistrer
                const croppedCanvas = cropper.getCroppedCanvas();

                // Convertissez le canevas croppé en une image base64
                let extension = $('#fileInput').val().replace(/^.*\./, '');
                const croppedImageData = croppedCanvas.toDataURL('image/'+extension);

                $.post("{{ route('admin.media.crop_update', [$media->id, request()->route('locale')]) }}",
                    {
                        'image': JSON.stringify({ image: croppedImageData }),
                        '_token': $('input[name="_token"]').val(),
                        'extension': extension
                    },
                    function(data, status){
                        if( status == "success" ){
                            window.location.href = "{{ route('admin.media.edit', [$media->id, request()->route('locale')]) }}";
                        }
                    });
            };


            // Gestion des boutons pour modifier le ratio de crop
            document.querySelectorAll('[data-crop]').forEach(button => {
                button.addEventListener('click', () => {
                    const ratio = button.getAttribute('data-crop');
                    cropper.setAspectRatio(eval(ratio));
                });
            });

            let isFlippedX = false;
            let isFlippedY = false;
            let coord = [1, 1];

            document.querySelectorAll('[data-scale]').forEach(button => {
                button.addEventListener('click', () => {
                    const scale = button.getAttribute('data-scale');
                    if(scale == 'y'){
                        if (isFlippedY) {
                            coord[1] = 1; // Si l'image est déjà retournée, ramenez-la à sa position d'origine
                        } else {
                            coord[1] = -1; // Inversez l'image verticalement
                        }
                        isFlippedY = !isFlippedY; // Basculez l'état de l'image (basculer entre vrai et faux)
                    }else{
                        if (isFlippedY) {
                            coord[0] = 1; // Si l'image est déjà retournée, ramenez-la à sa position d'origine
                        } else {
                            coord[0] = -1; // Inversez l'image verticalement
                        }
                        isFlippedX = !isFlippedX; // Basculez l'état de l'image (basculer entre vrai et faux)
                    }
                    cropper.scale(coord[0], coord[1]);
                });
            });

            document.querySelectorAll('[data-rotate]').forEach(button => {
                button.addEventListener('click', () => {
                    const rotate = button.getAttribute('data-rotate');
                    cropper.rotate(rotate);
                });
            });

            // Gestion des boutons pour zoomer et dézoomer
            document.querySelectorAll('[data-zoom]').forEach(button => {
                button.addEventListener('click', () => {
                    const value = parseFloat(button.getAttribute('data-zoom'));
                    cropper.zoom(value);
                });
            });

            ////

            const fileInput = document.getElementById('fileInput');

            // Event listener pour le changement de fichier
            fileInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                // Vérifiez si un fichier a été sélectionné
                if (file) {
                    // Créez un objet FileReader pour lire le fichier
                    const reader = new FileReader();

                    // Fonction de rappel lorsqu'une image est chargée
                    reader.onload = function (event) {
                        // Mettez à jour l'image affichée
                        image.src = event.target.result;

                        // Remplacez l'image dans Cropper
                        cropper.replace(event.target.result);
                    };

                    // Lire le contenu de l'image en tant que URL de données
                    reader.readAsDataURL(file);
                }
            });
            /////
        });

    </script>

@endsection