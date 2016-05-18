<?php

$module = new \App\Module($app); //\MartynBiz\Slim3Modules\Loader($app);

// homepages (index, about, etc)
$module->load('home');

// authentication (session, signup, etc)
$module->load('auth');
