<?php

// default settings
$settings = [
    // 'settings' => [
        'renderer' => [
            'template_path' => APPLICATION_PATH . '/modules/application/views',
        ],
    // ],
];

// // load environment settings
// if (file_exists(APPLICATION_ENV . '.php')) {
//     $settings = array_replace_recursive(
//         $settings,
//         require APPLICATION_ENV . '.php'
//     );
// }

return $settings;
