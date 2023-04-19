<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib\action\resp;

class RespFail extends RespOk
{
    public $message;

    public function __construct($message)
    {
        $this->ok = false;
        $this->message = $message;
    }
}
