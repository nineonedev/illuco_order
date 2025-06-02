<?php 

return [
    'locale' => 'ko',
    'fallback' => 'en',
    'default' => 'web',
    'translators' => [
        'web' => base_path('resources/lang'),
        'rule' => base_path('framework/Validation/locales') 
    ],
];