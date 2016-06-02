<?php

/**
 * A mockable replacement for file system functions
 */
namespace Wordup;

class FileSystem
{
    /**
     * @see http://php.net/manual/en/function.mkdir.php
     */
    public function makeDir($pathname, $mode='0777', $recursive=false)
    {
        return mkdir($pathname, $mode, $recursive);
    }

    /**
     * @see http://php.net/manual/en/function.file-exists.php
     */
    public function fileExists($file)
    {
        return file_exists($file);
    }

    /**
     * @see http://php.net/manual/en/function.file-get-contents.php
     */
    public function getFileContents($file)
    {
        return file_get_contents($file);
    }

    /**
     * @see http://php.net/manual/en/function.readfile.php
     */
    public function readFile($file)
    {
        return readfile($file);
    }
}
