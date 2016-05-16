<?php
namespace App\Model;

class Base extends \Illuminate\Database\Eloquent\Model
{
    const CREATED_AT = 'dt_create';
    const UPDATED_AT = 'dt_update';
    const DELETED_AT = 'dt_delete';
}
