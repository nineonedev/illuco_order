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
        base_path('resources/assets') => base_path('static'),  
        base_path('storage/uploads/public') => base_path('uploads'),
    ],
];