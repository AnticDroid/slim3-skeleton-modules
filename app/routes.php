<?php

$module = new \MartynBiz\Slim3Module\Module($app); //\MartynBiz\Slim3Modules\Loader($app);

// homepages (index, about, etc)
$module->load('home');

// authentication (session, signup, etc)
$module->load('auth');
