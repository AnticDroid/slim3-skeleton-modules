<?php

namespace Wordup\Model;

use MartynBiz\Mongo\Mongo;

/**
 *
 */
class Base extends Mongo
{
    public function getCreatedAt($value)
    {
        return date('d/m/Y h:i', $value->sec);
    }

    public function getUpdatedAt($value)
    {
        return date('d/m/Y h:i', $value->sec);
    }

    public function getDeletedAt($value)
    {
        return date('d/m/Y h:i', $value->sec);
    }
}
