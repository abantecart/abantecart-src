<?php
declare(strict_types = 1);

return [
    //text
    'html' => [
        'extension' => ['html', 'htm', 'php'],
        'mime-type' => ['text/html', 'application/xhtml+xml'],
        'charset' => true,
    ],
    'txt' => [
        'extension' => ['txt'],
        'mime-type' => ['text/plain'],
        'charset' => true,
    ],
    'css' => [
        'extension' => ['css'],
        'mime-type' => ['text/css'],
        'charset' => true,
    ],
    'json' => [
        'extension' => ['json'],
        'mime-type' => ['application/json', 'text/json', 'application/x-json'],
        'charset' => true,
    ],
    'jsonp' => [
        'extension' => ['jsonp'],
        'mime-type' => ['text/javascript', 'application/javascript', 'application/x-javascript'],
        'charset' => true,
    ],
    'js' => [
        'extension' => ['js'],
        'mime-type' => ['text/javascript', 'application/javascript', 'application/x-javascript'],
        'charset' => true,
    ],

    //xml
    'rdf' => [
        'extension' => ['rdf'],
        'mime-type' => ['application/rdf+xml'],
        'charset' => true,
    ],
    'rss' => [
        'extension' => ['rss'],
        'mime-type' => ['application/rss+xml'],
        'charset' => true,
    ],
    'atom' => [
        'extension' => ['atom'],
        'mime-type' => ['application/atom+xml'],
        'charset' => true,
    ],
    'xml' => [
        'extension' => ['xml'],
        'mime-type' => ['text/xml', 'application/xml', 'application/x-xml'],
        'charset' => true,
    ],
    'kml' => [
        'extension' => ['kml'],
        'mime-type' => ['application/vnd.google-earth.kml+xml'],
        'charset' => true,
    ],

    //images
    'bmp' => [
        'extension' => ['bmp'],
        'mime-type' => ['image/bmp'],
    ],
    'gif' => [
        'extension' => ['gif'],
        'mime-type' => ['image/gif'],
    ],
    'png' => [
        'extension' => ['png'],
        'mime-type' => ['image/png', 'image/x-png'],
    ],
    'jpg' => [
        'extension' => ['jpg', 'jpeg', 'jpe'],
        'mime-type' => ['image/jpeg', 'image/jpg'],
    ],
    'svg' => [
        'extension' => ['svg', 'svgz'],
        'mime-type' => ['image/svg+xml'],
    ],
    'psd' => [
        'extension' => ['psd'],
        'mime-type' => ['image/vnd.adobe.photoshop'],
    ],
    'eps' => [
        'extension' => ['ai', 'eps', 'ps'],
        'mime-type' => ['application/postscript'],
    ],
    'ico' => [
        'extension' => ['ico'],
        'mime-type' => ['image/x-icon', 'image/vnd.microsoft.icon'],
    ],

    //audio/video
    'mov' => [
        'extension' => ['mov', 'qt'],
        'mime-type' => ['video/quicktime'],
    ],
    'mp3' => [
        'extension' => ['mp3'],
        'mime-type' => ['audio/mpeg'],
    ],
    'mp4' => [
        'extension' => ['mp4'],
        'mime-type' => ['video/mp4'],
    ],
    'ogg' => [
        'extension' => ['ogg'],
        'mime-type' => ['audio/ogg'],
    ],
    'ogv' => [
        'extension' => ['ogv'],
        'mime-type' => ['video/ogg'],
    ],
    'webm' => [
        'extension' => ['webm'],
        'mime-type' => ['video/webm'],
    ],
    'webp' => [
        'extension' => ['webp'],
        'mime-type' => ['image/webp'],
    ],

    //fonts
    'eot' => [
        'extension' => ['eot'],
        'mime-type' => ['application/vnd.ms-fontobject'],
    ],
    'otf' => [
        'extension' => ['otf'],
        'mime-type' => ['font/opentype', 'application/x-font-opentype'],
    ],
    'ttf' => [
        'extension' => ['ttf'],
        'mime-type' => ['font/ttf', 'application/font-ttf', 'application/x-font-ttf'],
    ],
    'woff' => [
        'extension' => ['woff'],
        'mime-type' => ['font/woff', 'application/font-woff', 'application/x-font-woff'],
    ],
    'woff2' => [
        'extension' => ['woff2'],
        'mime-type' => ['font/woff2', 'application/font-woff2', 'application/x-font-woff2'],
    ],

    //other formats
    'pdf' => [
        'extension' => ['pdf'],
        'mime-type' => ['application/pdf', 'application/x-download'],
    ],
    'zip' => [
        'extension' => ['zip'],
        'mime-type' => ['application/zip', 'application/x-zip', 'application/x-zip-compressed'],
    ],
    'rar' => [
        'extension' => ['rar'],
        'mime-type' => ['application/rar', 'application/x-rar', 'application/x-rar-compressed'],
    ],
    'exe' => [
        'extension' => ['exe'],
        'mime-type' => ['application/x-msdownload'],
    ],
    'msi' => [
        'extension' => ['msi'],
        'mime-type' => ['application/x-msdownload'],
    ],
    'cab' => [
        'extension' => ['cab'],
        'mime-type' => ['application/vnd.ms-cab-compressed'],
    ],
    'doc' => [
        'extension' => ['doc'],
        'mime-type' => ['application/msword'],
    ],
    'rtf' => [
        'extension' => ['rtf'],
        'mime-type' => ['application/rtf'],
    ],
    'xls' => [
        'extension' => ['xls'],
        'mime-type' => ['application/vnd.ms-excel'],
    ],
    'ppt' => [
        'extension' => ['ppt'],
        'mime-type' => ['application/vnd.ms-powerpoint'],
    ],
    'odt' => [
        'extension' => ['odt'],
        'mime-type' => ['application/vnd.oasis.opendocument.text'],
    ],
    'ods' => [
        'extension' => ['ods'],
        'mime-type' => ['application/vnd.oasis.opendocument.spreadsheet'],
    ],
];
