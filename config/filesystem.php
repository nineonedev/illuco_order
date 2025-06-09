<?php 

return [
    'default' => 'local',
    'disks' => [
        'local'     => base_path('storage/uploads/public'), 
        'private'   => base_path('storage/uploads/private'), 
        'temp'      => base_path('storage/tmp'), 
        'cache'     => base_path('storage/cache'), 
    ],
    'symlinks' => [
        base_path('resources/assets') => base_path('static/app'),  
        base_path('storage/uploads/public') => base_path('/static/uploads'),
    ],
    'mimetypes' => [
        'image' => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/svg+xml', 'image/x-icon',
        ],
        'document' => [
            'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain', 'application/rtf', 'application/xml', 'text/csv',
        ],
        'archive' => [
            'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed', 'application/x-tar',
            'application/gzip', 'application/x-bzip2',
        ],
        'audio' => [
            'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/aac', 'audio/flac', 'audio/mp3',
        ],
        'video' => [
            'video/mp4', 'video/mpeg', 'video/x-msvideo', 'video/x-ms-wmv', 'video/webm', 'video/ogg', 'video/quicktime',
        ],
    ]
];