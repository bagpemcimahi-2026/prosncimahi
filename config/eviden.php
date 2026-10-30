<?php

return [
    // Path file JSON service account Google
    'credentials' => env('EVIDEN_GOOGLE_CREDENTIALS', storage_path('app/google/service-account.json')),

    // ID folder utama (bagian setelah /folders/ di link Drive)
    'folder_sem1' => env('EVIDEN_FOLDER_SEM1'),
    'folder_sem2' => env('EVIDEN_FOLDER_SEM2'),

    // Lama cache hasil scan Drive (detik)
    'cache_ttl' => (int) env('EVIDEN_CACHE_TTL', 600),
];
