<?php

// this is app config

return [
    'settings' => [

        'modules' => [],

        // 'renderer' => [
        //     'folders' => [
        //         'template_path' => APPLICATION_PATH . '/views',
        //     ],
        //     'ext' => 'phtml'
        // ],

        'session' => [
            'namespace' => 'slim3__',
        ],

        'auth' => [

            // this is the session namespace. apps that want to authenticate
            // using this auth app must configure their mwauth-client to match
            'namespace' => 'slim3__auth__',

            // // remember me cookie settings
            // 'auth_token' => [
            //     'cookie_name' => 'auth_token',
            //     'expire' => strtotime("+3 months", time()), // seconds from now
            //     'path' => '/',
            // ],

            // // remember me cookie settings
            // 'recovery_token' => [
            //     'expire' => strtotime("+1 hour", time()), // seconds from now
            // ],

            // these are attributes that will be written to session
            'valid_attributes' => [
                'id',
                'first_name',
                'last_name',
                'name',
                'email',
                'facebook_id',
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
        //     'file_path' => APPLICATION_PATH . '/modules/Application/language/',
        // ],

        // 'mail' => [
        //
        //     // directory where suppressed email files are written to in non-prod env
        //     'file_path' => APPLICATION_PATH . '/data/mail/',
        // ],
    ],
];
