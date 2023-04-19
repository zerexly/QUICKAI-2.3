<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib\action;

use EdSDK\FlmngrServer\lib\action\resp\RespFail;

class ActionError extends AAction
{
    public function getName()
    {
        return 'error';
    }

    public function run($req)
    {
        return new RespFail($req->message);
    }
}
