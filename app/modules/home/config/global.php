<?php

// TODO put this into config

// default settings
$settings = [

    // Renderer settings
    'renderer' => [

        // this is the directory where view scripts are stored
        'template_path' => APPLICATION_PATH . '/modules/home/views/',

        // when using blade templates, we have a cache dir. for php templates
        // this probably isn't used.
        'cache_path' => APPLICATION_PATH . '/../data/cache/blade',
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
