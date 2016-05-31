<?php

// TODO put this into config

// default settings
$settings = [
    'modules' => [
        'auth_register' => [
            'renderer' => [
                'template_path' => APPLICATION_PATH . '/modules/auth_register/views',
            ],
        ],
    ],
];

// load environment settings
if (file_exists(APPLICATION_ENV . '.php')) {
    $settings = array_replace_recursive(
        $settings,
        require APPLICATION_ENV . '.php'
    );
}

return $settings;
