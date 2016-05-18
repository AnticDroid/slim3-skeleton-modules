<?php
namespace App\Modules\Auth\Adapter;

interface AdapterInterface //implements AdapterInterface
{
    /**
     * Performs an authentication attempt
     */
    public function authenticate($identity, $password);
}
