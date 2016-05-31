<?php

// TODO put this into config

// default settings
$settings = [
    'modules' => [
        'auth' => [
            'renderer' => [
                'template_path' => APPLICATION_PATH . '/modules/auth/views',
            ],
        ],
    ],
];

// load environment settings
if (file_exists(APPLICATION_PATH . '/modules/home/config/' . APPLICATION_ENV . '.php')) {
    $settings = array_replace_recursive(
        $settings,
        require APPLICATION_PATH . '/modules/home/config/' . APPLICATION_ENV . '.php'
    );
}

return $settings;
