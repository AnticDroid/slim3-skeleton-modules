<?php
namespace App\Auth\Storage;

/**
 * This ArrayAccess implementation is designed to allow session variables to be
 * accessed in an OOP method (ie. for mocking during unit testing ) BUT not put
 * objects into session (e.g. like Zend\Session does)
 */
interface StorageInterface extends \ArrayAccess
{
    /**
     * Will get the contents from wherever stored (session, db) and return as a
     * PHP associative array
     * @return array
     */
    public function getContents();

    /**
     * Will empty the contents of the storage (e.g. clear session sso variables )
     * @return void
     */
    public function emptyContents();
}
