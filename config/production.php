<?php

// default settings
return [
    'settings' => [
        'displayErrorDetails' => false,

        // when a user logs in or out, and returnTo is not given, the app will
        // return to these URLs. If these are not set, it will redirect to /session
        'defaultLoginRedirect' => 'http://japantravel.com/login?returnTo=/',
        'defaultLogoutRedirect' => 'http://japantravel.com/logout?returnTo=/',

        'eloquent' => [
    		'database' => 'jt_sso',
    		'username' => 'jtuser',
    		'password' => 'Schneewittchen3396',
        ],

        'session' => [

            // this is the cookie domain that PHPSESSID will be set to. it enabled
            // us to share the session variables across multiple domains
            'cookie_domain' => '.japantravel.com',
        ],

        // this is the domain of the applications that are sharing the session
        // a valid returnTo url is one that contains this string
        'valid_return_to' => '/japantravel\.com$/', // matches "jt.martyndev", "en.japantravel.com", "admin.jt.martyndev"

        // this is the domain of the mwauth installation, used for social media auth callbacks
        'app_domain' => 'http://sso.japantravel.com',

        'auth' => [
            'cookie_domain' => '.japantravel.com',
        ],

        'facebook' => [
            'app_id' => '246281065429543',
            'secret' => '5cc4454a4b90b7df90b1ac899a41499d',
            'version' => 'v2.0',
        ],
    ],
];
