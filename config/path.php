<?php 

return [
    'view'          => base_path('resources/views'),
    'asset'         => base_path('static/app'),
    'uploads'       => base_path('static/uploads'),
    'helpers'       => base_path('framework/Support/Helpers'),

    'bootstrap' => [
        'bootstrappers'     => base_path('bootstrap/bootstrappers.php'),
        'aliases'           => base_path('bootstrap/aliases.php'),
        'providers'         => base_path('bootstrap/providers.php'),
        'middlewares'       => base_path('bootstrap/middlewares.php'),
        'relations'         => base_path('bootstrap/relations.php'),
        'commands'          => base_path('bootstrap/commands.php'),
    ],
];