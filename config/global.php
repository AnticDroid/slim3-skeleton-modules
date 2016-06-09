<?php

// this is app config

return [
    'settings' => [

        'module_initializer' => [
            'autoload' => [
                'Application',
                'Auth',
                'Blog',
                'Hello',
            ],
            'modules_path' => APPLICATION_PATH . '/modules',
        ],
    ],
];
