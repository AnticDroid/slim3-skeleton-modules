<?php

namespace Blog\Model;

use Application\Model\Base;

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
    );

    // public function validate()
    // {
    //     $this->resetErrors();
    //
    //     if (empty($this->data['name'])) {
    //         $this->setError('Name must be given');
    //     }
    //
    //     if (empty($this->data['slug'])) {
    //         $this->setError('Slug must be given');
    //     }
    //
    //     return empty($this->getErrors());
    // }
}
