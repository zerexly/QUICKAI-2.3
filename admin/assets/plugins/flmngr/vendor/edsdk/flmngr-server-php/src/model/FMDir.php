<?php

/**
 * Flmngr Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\model;

class FMDir
{
    private $path; // contains parent dir's path WITHOUT starting AND trailing "/"
    private $name;

    public $f;
    public $d;
    public $p; // property exists in PHP version only, for JSON generation

    function __construct($name, $path, $filesCount, $dirsCount)
    {
        $this->path = $path;
        $this->name = $name;
        $this->f = $filesCount;
        $this->d = $dirsCount;

        $this->p =
            (strlen($this->path) > 0 ? '/' . $this->path : '') .
            '/' .
            $this->name;
    }
}
