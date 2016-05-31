<?php

// filesystem
$container['fs'] = function ($c) {
    return new \Wordup\FileSystem();
};

// filesystem
$container['image'] = function ($c) {
    return new \Wordup\Image();
};

// photo_manager
// deals with moving
$container['photo_manager'] = function ($c) {
    return new \Wordup\PhotoManager($c['image'], $c['fs']);
};

$container['model.article'] = function ($c) {
    return new \Wordup\Model\Article();
};

$container['model.tag'] = function ($c) {
    return new \Wordup\Model\Tag();
};

$container['model.photo'] = function ($c) {
    return new \Wordup\Model\Photo();
};
