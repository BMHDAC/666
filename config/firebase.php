<?php

return [
    "crendentials" => [
        'file' => env('FIREBASE_CREDENTIALS', base_path('firebase_services.json')),
        'auto_discovery' => false
    ],
];
