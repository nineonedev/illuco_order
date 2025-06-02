<?php 

return [
    'view' => base_path('resources/views'),
    'asset' => base_path('static'),
    'bootstrap' => [
        'aliases' => base_path('bootstrap/aliases.php'),
        'bootstrappers' => base_path('bootstrap/aliases.php'),
        'paths' => base_path('bootstrap/paths.php'),
        'priority' => base_path('bootstrap/priority.php'),
        'providers' => base_path('bootstrap/providers.php'),
    ],
];