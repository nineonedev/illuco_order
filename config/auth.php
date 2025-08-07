<?php 

return [
    'remember' => [
        'ttl' => 60 * 60 * 24 * 30, // 30일
    ],
    'session' => [
        'lifetime' => 60 * 60, // 30분
        'gc_probability' => 1 // 퍼센트 (1% 확률)
    ],
];