<?php

return [

    'path' => 'uploads/',
    'repertoires' => [
        'article',
    ],
    'types' => [
        'image' => [
            'type' => 'image',
            'mimes' => ['jpeg', 'jpg','png','bmp','gif'],
        ],
        'document' => [
            'type' => 'document',
            'mimes' => ['pdf'],
            'icone' => 'fa fa-file',
        ],
    ],
    'upload_max_filesize' => 10000,
    'alignements' => [
        '' => 'Aucun',
        'img-center' => 'Centré',
        'img-left' => 'Gauche',
        'img-right' => 'Droite',
    ],
    'tailles' => [
        '' => 'Original',
        '150x150' => 'Miniature',
        '300' => 'Moyenne',
        '1200' => 'Grande',
    ],
    

];
