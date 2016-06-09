<?php

// 1) load global
$settings = [
    'settings' => [
        'module_initializer' => [
            'Application',
            'Auth',
            'Blog',
            'Hello',
        ],
    ],
];

// 2) TODO load modules config here before autoload

// 3) autoload stuff, module config here will overwrite default
$autoloadPath = realpath('autoload/');
if ($autoloadPath) {
    foreach (scandir($autoloadPath) as $file) {
        if ('.' === $file) continue;
        if ('..' === $file) continue;

        $settings = array_replace_recursive(
            $settings,
            require $autoloadPath . $file
        );
    }
}

// 4) overwrite all with environment config
if (file_exists(__DIR__ . '/' . APPLICATION_ENV . '.php')) {
    $settings = array_replace_recursive(
        $settings,
        require __DIR__ . '/' . APPLICATION_ENV . '.php'
    );
}

return $settings;
