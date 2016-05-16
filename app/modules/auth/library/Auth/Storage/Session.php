<?php
namespace App\Auth\Storage;

/**
 * This ArrayAccess implementation is designed to allow session variables to be
 * accessed in an OOP method (ie. for mocking during unit testing ) BUT not put
 * objects into session (e.g. like Zend\Session does)
 */
class Session implements StorageInterface
{
    protected $contents;

    public function __construct($namespace, $values=array())
    {
        // if namespace doesn't exist, create it as an empty array
        if (! array_key_exists($namespace, $_SESSION))
            $_SESSION[$namespace] = array();

        // merge values into the current namespace (don't think we wanna overwrite)
        $_SESSION[$namespace] = array_merge($_SESSION[$namespace], $values);

        // create referrence to namespace
        $this->contents = $_SESSION[$namespace];
    }

    public function offsetExists($index) {
        return isset($this->contents[$index]);
    }

    public function offsetGet($index) {
        if($this->offsetExists($index)) {
            return $this->contents[$index];
        }
        return false;
    }

    public function offsetSet($index, $value) {
        if($index) {
            $this->contents[$index] = $value;
        } else {
            $this->contents[] = $value;
        }
        return true;

    }

    public function offsetUnset($index) {
        unset($this->contents[$index]);
        return true;
    }

    /**
     * Will get the contents from wherever stored (session, db) and return as a
     * PHP associative array
     * @return array
     */
    public function getContents() {
        return $this->contents;
    }

    /**
     * Will empty the contents of the storage (e.g. clear session sso variables )
     * @return void
     */
    public function emptyContents() {
        return $this->contents = array();
    }
}
