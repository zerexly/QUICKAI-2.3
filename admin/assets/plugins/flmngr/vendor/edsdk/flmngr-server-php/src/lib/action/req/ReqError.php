<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib\action\req;

class ReqError extends Req
{
    public $message;

    public static function createReqError($msg)
    {
        $req = new ReqError();
        $req->message = $msg;
        $req->action = 'error';
        return $req;
    }
}
