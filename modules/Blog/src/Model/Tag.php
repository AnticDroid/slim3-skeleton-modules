<?php

namespace Blog\Model;

use Application\Model\Base;
use Blog\Utils;

/**
 *
 */
class Tag extends Base
{
    // collection this model refers to
    protected static $collection = 'tags';

    // define on the fields that can be saved
    protected static $whitelist = array(
        'name',
        'slug',
        'public',
    );

    /**
     * Ensure that public is numeric
     */
    public function setPublic($value)
    {
        return (int) $value;
    }

    /**
     * Additional Save procedures
     */
    public function save($data=array())
    {
        // TODO check if a slug of the same name exists
        $this->set($data);
        $this->data['slug'] = Utils::slugify($this->data['name']);
        return parent::save();
    }
}
