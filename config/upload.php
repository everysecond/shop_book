<?php

return [
    'allow_image_ext'      => [
        // Images
        'jpg',
        'jpeg',
        'gif',
        'png',
        'bmp'
//         Shell
//        'sh',
//        'csv',
//        'xml'
    ],
    'allow_file_ext'      => [
        // Excel
        'xls',
        'xlsx',
    ],
    'allow_music_ext'      => [
        // music
        'mp3',
        'wav',
        'ogg',
    ],
    'max_size'       => '2', // MB
    'attachment_url' => env("APP_URL","cps.koodsoft.com").env('APP_ATTACHMENT_URL', '/storage'),
    'prefix'=>"/storage"
];