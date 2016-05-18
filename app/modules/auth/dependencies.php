<?php

// Models
$container['model.user'] = function ($c) {
    return new \App\Modules\Auth\Model\User();
};
