<?php

// default settings
return [
    'settings' => [
        'displayErrorDetails' => true,

        'defaultLoginRedirect' => 'http://jt2.staging.metroworks.co.jp/login?returnTo=/',
        'defaultLogoutRedirect' => 'http://jt2.staging.metroworks.co.jp/logout?returnTo=/',

        'eloquent' => [
    		'database' => 'sso_staging',
    		'username' => 'sso_staging_user',
    		'password' => 'Eiche6891',
        ],

        'session' => [

            // this is the cookie domain that PHPSESSID will be set to. it enabled
            // us to share the session variables across multiple domains
            'cookie_domain' => '.jt2.staging.metroworks.co.jp',
        ],

        // this is the domain of the applications that are sharing the session
        // a valid returnTo url is one that contains this string
        'valid_return_to' => '/jt2\.staging\.metroworks\.co\.jp$/', // matches "*.jt2.staging.metroworks.co.jp/...", "en.jt2.staging.metroworks.co.jp", "admin.jt.martyndev"

        // this is the domain of the mwauth installation, used for social media auth callbacks
        'app_domain' => 'http://sso.jt2.staging.metroworks.co.jp',

        'auth' => [
            'cookie_domain' => '.jt2.staging.metroworks.co.jp',
        ],

        'facebook' => [
            'app_id' => '952593968131579',
            'secret' => 'dd15a4bfd4f7901c70d9a8b0058c22cf',
            'version' => 'v2.5',
        ],
    ],
];
