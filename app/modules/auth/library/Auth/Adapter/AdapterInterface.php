<?php
namespace App\Auth\Adapter;

interface AdapterInterface //implements AdapterInterface
{
    /**
     * Performs an authentication attempt
     */
    public function authenticate($identity, $password);
}
