<?php

// TODO put this into config

// default settings
$settings = [

    // Renderer settings
    'renderer' => [

        // this is the directory where view scripts are stored
        'template_path' => APPLICATION_PATH . '/modules/auth/views',
    ],
];

// load environment settings
if (file_exists(APPLICATION_PATH . '/config/' . APPLICATION_ENV . '.php')) {
    $settings = array_replace_recursive(
        $settings,
        require APPLICATION_PATH . '/config/' . APPLICATION_ENV . '.php'
    );
}

return $settings;
