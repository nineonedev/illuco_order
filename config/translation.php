<?php 

return [
    'locale' => 'ko',
    'fallback' => 'en',
    'default' => 'app',
    'translators' => [
        'app' => base_path('resources/lang/app'),
        'rule' => base_path('resources/lang/rule'),
        'validation' => base_path('resources/lang/validation'),
        'system' => base_path('resources/lang/system'),
    ],
    'languages' => [
        'ko' => '한국어',
        'en' => 'English',
    ],
];