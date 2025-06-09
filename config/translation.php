<?php 

return [
    'locale' => 'ko',
    'fallback' => 'en',
    'default' => 'web',
    'translators' => [
        'web' => base_path('resources/lang'),
        'rule' => base_path('framework/Validation/locales'),
        'system' => base_path('resources/system'),
    ],
    'languages' => [
        'ko' => '한국어',
        'en' => 'English',
    ],
];