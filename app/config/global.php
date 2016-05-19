<?php

// TODO put this into config

// default settings
$settings = [
    'settings' => [

        // Renderer settings
        'renderer' => [

            // this is the directory where view scripts are stored
            'template_path' => APPLICATION_PATH . '/views/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => APPLICATION_PATH . '/../data/logs/app.log',
        ],

        // this is the domain of the applications that are sharing the session
        // a valid returnTo url is one that contains this string
        'valid_return_to' => '/localhost$/',

        // 'eloquent' => [
        //     'driver' => 'mysql',
    	// 	'host' => 'localhost',
        //     'charset' => 'utf8',
        //     'collation' => 'utf8_unicode_ci',
        //     'prefix' => '',
        // ],

        'mongo' => [
            'db' => 'wordup',
            // 'username' => 'myuser',
            // 'password' => 'mypass',
        ],

        'session' => [
            'namespace' => 'jt_sso__',
        ],

        'auth' => [

            // this is the session namespace. apps that want to authenticate
            // using this auth app must configure their mwauth-client to match
            'namespace' => 'jt_sso__',

            // remember me cookie settings
            'auth_token' => [
                'cookie_name' => 'auth_token',
                'expire' => strtotime("+3 months", time()), // time in seconds from now, e.g. 1440 = 1h from now
                'path' => '/',
            ],

            // these are attributes that will be written to session
            'valid_attributes' => [
                'first_name',
                'last_name',
                'name',
                'email',
                'username',
                'name',
                'id',
                'facebook_id',
                'backend',
            ],
        ],

        // 'i18n' => [
        //
        //     // when the target locale is missing a translation/ template this the
        //     // fallback locale to use (probably "en")
        //     'default_locale' => 'en',
        //
        //     // this is the type of the translation files using by zend-i18n
        //     'type' => 'phparray',
        //
        //     // where the translation files are stored
        //     'file_path' => APPLICATION_PATH . '/i18n/',
        // ],

        // 'mail' => [
        //
        //     // directory where suppressed email files are written to in non-prod env
        //     'file_path' => APPLICATION_PATH . '/../data/mail/',
        // ],

        // // remember me cookie settings
        // 'recovery_token' => [
        //     'expire' => strtotime("+1 hour", time()), // time in seconds from now, e.g. 1440 = 1h from now
        // ],
    ],
];

// load environment settings
if (file_exists(APPLICATION_PATH . '/config/' . APPLICATION_ENV . '.php')) {
    $settings = array_replace_recursive(
        $settings,
        require APPLICATION_PATH . '/config/' . APPLICATION_ENV . '.php'
    );
}

// // load any private settings (eg. database credentials)
// if (file_exists(APPLICATION_PATH . '/config/local.php')) {
//     $settings = array_replace_recursive(
//         $settings,
//         require APPLICATION_PATH . '/config/local.php'
//     );
// }

return $settings;
