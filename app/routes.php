<?php

$modules = new \App\Loader($app); //\MartynBiz\Slim3Modules\Loader($app);

// homepages (index, about, etc)
$modules->load('home');

// authentication (session, signup, etc)
$modules->load('auth');

// // articles
// $modules->load('articles');
//
// // articles
// $modules->load('admin');
